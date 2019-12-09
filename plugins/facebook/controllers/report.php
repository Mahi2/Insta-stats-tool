<?php
defined('ALTUMCODE') || die();

/* We need to check if the user already exists in our database */
$source_account = Database::get('*', 'facebook_users', ['username' => $user]);

if($refresh || !$source_account || ($source_account && (new \DateTime())->modify('-'.$settings->facebook_check_interval.' hours') > (new \DateTime($source_account->last_check_date)))) {

    $facebook = new Facebook();

    /* Set proxy if needed */
    if($is_proxy_request) {
        $facebook::set_proxy($is_proxy_request);
    }

    try {
        $source_account_data = $facebook->get($user);
    } catch (Exception $error) {
        $_SESSION['error'][] = $error->getCode() == 404 ? $language->facebook->report->error_message->not_found : $error->getMessage();

        /* Make sure to set the failed request to the proxy */
        if($is_proxy_request) {
            if($error->getCode() == 404) {
                $database->query("UPDATE `proxies` SET `failed_requests` = `failed_requests` + 1, `total_failed_requests` = `total_failed_requests` + 1, `last_date` = '{$date}' WHERE `proxy_id` = {$proxy->proxy_id}");
            } else {
                $database->query("UPDATE `proxies` SET `last_date` = '{$date}' WHERE `proxy_id` = {$proxy->proxy_id}");
            }
        }

        redirect();
    }


    /* Make sure to set the successful request to the proxy */
    if($is_proxy_request) {

        if($proxy->failed_requests >= $settings->proxy_failed_requests_pause) {
            Database::update('proxies', ['failed_requests' => 0, 'successful_requests' => 1, 'last_date' => $date], ['proxy_id' => $proxy->proxy_id]);
        } else {
            $database->query("UPDATE `proxies` SET `successful_requests` = `successful_requests` + 1, `total_successful_requests` = `total_successful_requests` + 1, `last_date` = '{$date}' WHERE `proxy_id` = {$proxy->proxy_id}");
        }

    }


    /* Check if the account needs to be added and has more than needed followers */
    if(!$source_account) {
        if($source_account_data->likes < $settings->facebook_minimum_likes) {
            $_SESSION['error'][] = sprintf($language->facebook->report->error_message->low_likes, $settings->facebook_minimum_likes);
        }

        if(!empty($_SESSION['error'])) redirect();

    }

    /* Vars to be added & used */
    $source_account_new = new StdClass();
    $source_account_new->username = $user;
    $source_account_new->name = $source_account_data->name;
    $source_account_new->likes = $source_account_data->likes;
    $source_account_new->followers = $source_account_data->followers;
    $source_account_new->profile_picture_url = $source_account_data->profile_picture_url;
    $source_account_new->followers = $source_account_data->followers;
    $source_account_new->is_verified = (int) $source_account_data->is_verified;

    /* Get extra details from last media */
    $details = [
        'type'      => $source_account_data->type,
    ];
    $details = json_encode($details);

    /* Insert into db */
    $stmt = $database->prepare("INSERT INTO `facebook_users` (
        `username`,
        `name`,
        `likes`,
        `followers`,
        `details`,
        `profile_picture_url`,
        `is_verified`,
        `added_date`,
        `last_check_date`,
        `last_successful_check_date`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        `username` = VALUES (username),
        `name` = VALUES (name),
        `likes` = VALUES (likes),
        `followers` = VALUES (followers),
        `profile_picture_url` = VALUES (profile_picture_url),
        `is_verified` = VALUES (is_verified),
        `last_check_date` = VALUES (last_check_date),
        `last_successful_check_date` = VALUES (last_successful_check_date)
    ");
    $stmt->bind_param('ssssssssss',
        $source_account_new->username,
        $source_account_new->name,
        $source_account_new->likes,
        $source_account_new->followers,
        $details,
        $source_account_new->profile_picture_url,
        $source_account_new->is_verified,
        $date,
        $date,
        $date
    );
    $stmt->execute();
    $stmt->close();

    /* Retrieve the just created / updated row */
    $source_account = Database::get('*', 'facebook_users', ['username' => $user]);

    /* Update or insert the check log */
    $log = $database->query("SELECT `id` FROM `facebook_logs` WHERE `facebook_user_id` = '{$source_account->id}' AND DATEDIFF('{$date}', `date`) = 0")->fetch_object();

    if($log) {
        Database::update(
            'facebook_logs',
            [
                'likes' => $source_account->likes,
                'followers' => $source_account->followers,
                'date' => $date
            ],
            ['id' => $log->id]
        );
    } else {
        $stmt = $database->prepare("INSERT INTO `facebook_logs` (
            `facebook_user_id`,
            `username`,
            `likes`,
            `followers`,
            `date`
        ) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss',
            $source_account->id,
            $source_account->username,
            $source_account->likes,
            $source_account->followers,
            $date
        );
        $stmt->execute();
        $stmt->close();
    }
}

/* Retrieve last X entries */
$logs = [];

if($date_start && $date_end) {
    $date_start_query = (new DateTime($date_start))->format('Y-m-d H:i:s');
    $date_end_query = (new DateTime($date_end))->modify('+1 day')->format('Y-m-d H:i:s');

    $logs_result = $database->query("SELECT * FROM `facebook_logs` WHERE `facebook_user_id` = '{$source_account->id}' AND (`date` BETWEEN '{$date_start_query}' AND '{$date_end_query}')  ORDER BY `date` DESC");
} else {
    $logs_result = $database->query("SELECT * FROM `facebook_logs` WHERE `facebook_user_id` = '{$source_account->id}' ORDER BY `date` DESC LIMIT 15");
}


while($log = $logs_result->fetch_assoc()) { $logs[] = $log; }
$logs = array_reverse($logs);

/* Generate data for the charts and retrieving the average followers /uploads per day */
$logs_chart = [
    'labels'                    => [],
    'likes'                     => [],
    'followers'                   => [],
];

$total_new = [
    'likes' => [],
    'followers' => []
];

for($i = 0; $i < count($logs); $i++) {
    $logs_chart['labels'][] = (new \DateTime($logs[$i]['date']))->format($language->global->date->datetime_format);
    $logs_chart['likes'][] = $logs[$i]['likes'];
    $logs_chart['followers'][] = $logs[$i]['followers'];

    if($i != 0) {
        $total_new['likes'][] = $logs[$i]['likes'] - $logs[$i - 1]['likes'];
        $total_new['followers'][] = $logs[$i]['followers'] - $logs[$i - 1]['followers'];
    }
}

/* reverse it back */
$logs = array_reverse($logs);

/* Defining the chart data */
$logs_chart = generate_chart_data($logs_chart);

/* Defining the future projections data */
$total_days = count($logs) > 1 ? (new \DateTime($logs[count($logs)-1]['date']))->diff((new \DateTime($logs[1]['date'])))->format('%a') : 0;

$average = [
    'likes'                 => $total_days > 0 ? (int) ceil(array_sum($total_new['likes']) / $total_days) : 0,
    'followers'    => $total_days > 0 ? (int) ceil((array_sum($total_new['followers']) / $total_days)) : 0
];


$source_account->details = json_decode($source_account->details);

/* Custom title */
add_event('title', function() {
    global $page_title;
    global $user;
    global $language;

    $page_title = sprintf($language->facebook->report->title, $user);
});

