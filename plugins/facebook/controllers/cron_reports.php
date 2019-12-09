<?php
defined('ALTUMCODE') || die();

/* We need to get the next users that we are going to check */
if($settings->store_unlock_report_price == '0' || ($settings->store_unlock_report_price != '0' && $settings->cron_mode == 'ALL')) {
    $result = $database->query("
        SELECT `username`, `name`, `last_check_date`, `id`
        FROM `facebook_users`
        WHERE TIMESTAMPDIFF(HOUR, `last_check_date`, '{$date}') > {$settings->facebook_check_interval} 
        ORDER BY `last_check_date` ASC
        LIMIT {$settings->cron_queries}
    ");
} else if($settings->store_unlock_report_price != '0') {
    $result = $database->query("
        SELECT  `unlocked_reports`.`date`, `unlocked_reports`.`expiration_date`, `facebook_users`.`username`, `facebook_users`.`name`, `facebook_users`.`last_check_date`, `facebook_users`.`id`
        FROM `unlocked_reports` 
        LEFT JOIN `facebook_users` ON `unlocked_reports`.`source_user_id` = `facebook_users`.`id` 
        WHERE TIMESTAMPDIFF(HOUR, `facebook_users`.`last_check_date`, '{$date}') > {$settings->facebook_check_interval}
        ORDER BY `facebook_users`.`last_check_date` ASC
        LIMIT {$settings->cron_queries}
    ");
}


/* Iterate through the results */
while($source_account = $result->fetch_object()) {
    if(DEBUG) { echo 'Facebook Account Check: '; print_r($source_account); echo '<br />'; }

    $user = $source_account->username;

    $facebook = new Facebook();

    /* Set proxy if needed */
    if($is_proxy_request) {
        $facebook::set_proxy($is_proxy_request);
    }


    try {
        $source_account_data = $facebook->get($user);
    } catch (Exception $error) {
        $error_message = $error->getMessage();

        /* Make sure to set the failed request to the proxy */
        if($is_proxy_request) {
            if($error->getCode() == 404) {
                $database->query("UPDATE `proxies` SET `failed_requests` = `failed_requests` + 1, `total_failed_requests` = `total_failed_requests` + 1, `last_date` = '{$date}' WHERE `proxy_id` = {$proxy->proxy_id}");
            } else {
                $database->query("UPDATE `proxies` SET `last_date` = '{$date}' WHERE `proxy_id` = {$proxy->proxy_id}");
            }
        }

        /* Update the user so it will not get checked again until it's time comes */
        Database::update(
            'facebook_users',
            ['last_check_date' => $date],
            ['id' => $source_account->id]
        );


        if(DEBUG) { echo 'Something happened, error:'; print_r($error_message); echo '<br />'; }

        /* If the account is not existing anymore, remove it */
        if($error->getCode() == 404) {

            $database->query("DELETE FROM `facebook_users` WHERE `id` = '{$source_account->id}'");
            $database->query("DELETE FROM `favorites` WHERE `source_user_id` = '{$source_account->id}' AND `source` = 'FACEBOOK'");
            $database->query("DELETE FROM `email_reports` WHERE `source_user_id` = '{$source_account->id}' AND `source` = 'FACEBOOK'");

            if(DEBUG) { echo 'User ' . $user . ' was deleted from the database beacause it does not exist anymore'; echo '<br />'; }

        }

        continue;
    }

    /* Make sure to set the successful request to the proxy */
    if($is_proxy_request) {

        if($proxy->failed_requests >= $settings->proxy_failed_requests_pause) {
            Database::update('proxies', ['failed_requests' => 0, 'successful_requests' => 1, 'last_date' => $date], ['proxy_id' => $proxy->proxy_id]);
        } else {
            $database->query("UPDATE `proxies` SET `successful_requests` = `successful_requests` + 1, `total_successful_requests` = `total_successful_requests` + 1, `last_date` = '{$date}' WHERE `proxy_id` = {$proxy->proxy_id}");
        }

    }


    /* Vars to be added & used */
    $source_account_new = new StdClass();
    $source_account_new->username = $user;
    $source_account_new->name = $source_account_data->name;
    $source_account_new->likes = $source_account_data->likes;
    $source_account_new->followers = $source_account_data->followers;
    $source_account_new->profile_picture_url = $source_account_data->profile_picture_url;
    $source_account_new->is_verified = (int) $source_account_data->is_verified;

    /* Get extra details from last media */
    $details = [
        'type'  => $source_account_data->type
    ];
    $details = json_encode($details);


    /* Update the user main data */
    $stmt = $database->prepare("UPDATE `facebook_users` SET
        `name` = ?,
        `likes` = ?,
        `followers` = ?,
        `details` = ?,
        `profile_picture_url` = ?,
        `is_verified` = ?,
        `last_check_date` = ?,
        `last_successful_check_date` = ?
        
        WHERE `id` = ?
    ");
    $stmt->bind_param('sssssssss',
        $source_account_new->name,
        $source_account_new->likes,
        $source_account_new->followers,
        $details,
        $source_account_new->profile_picture_url,
        $source_account_new->is_verified,
        $date,
        $date,
        $source_account->id
    );
    $stmt->execute();
    $stmt->close();


    /* Retrieve the just created / updated row */
    $source_account = Database::get('*', 'facebook_users', ['id' => $source_account->id]);

    /* Get the current day log */
    $log = $database->query("SELECT * FROM `facebook_logs` WHERE `facebook_user_id` = '{$source_account->id}' ORDER BY `id` DESC")->fetch_object();

    if($log) {
        $current_date = (new \DateTime())->format('Y-m-d');
        $days_difference = (new \DateTime((new \DateTime($log->date))->format('Y-m-d')))->diff((new \DateTime($current_date)))->format('%a');

        /* Try to auto calculate the missing days in between the last log and today */
        if($days_difference > 0 && $settings->cron_auto_add_missing_logs) {

            /* Insert in between logs */
            for($i = 1; $i < $days_difference; $i++) {
                $is_generated = 1;

                $spread = [
                    'followers' => floor($log->followers + $i * (($source_account->followers - $log->followers) / $days_difference)),
                    'likes' => floor($log->likes + $i * (($source_account->likes - $log->likes) / $days_difference)),
                    'date' => (new DateTime($log->date))->modify('+' . $i . ' days')->format('Y-m-d H:i:s')
                ];

                $stmt = $database->prepare("INSERT INTO `facebook_logs` (
                    `facebook_user_id`,
                    `username`,
                    `followers`,
                    `likes`,
                    `date`
                ) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('sssss',
                    $source_account->id,
                    $source_account->username,
                    $spread['followers'],
                    $spread['likes'],
                    $spread['date']
                );
                $stmt->execute();
                $stmt->close();

                if(DEBUG) { echo 'Inserted in between log ' . print_r($spread) . ' <br /><br />'; }

            }

        }

        if($days_difference == 0) {

            Database::update(
                'facebook_logs',
                [
                    'followers' => $source_account->followers,
                    'likes' => $source_account->likes,
                    'date' => $date
                ],
                ['id' => $log->id]
            );

        }
    }

    /* If no log is existing in the current day, insert it  */
    if(!$log || ($log && $days_difference > 0)) {

        $stmt = $database->prepare("INSERT INTO `facebook_logs` (
            `facebook_user_id`,
            `username`,
            `followers`,
            `likes`,
            `date`
        ) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss',
            $source_account->id,
            $source_account->username,
            $source_account->followers,
            $source_account->likes,
            $date
        );
        $stmt->execute();
        $stmt->close();

    }

}
