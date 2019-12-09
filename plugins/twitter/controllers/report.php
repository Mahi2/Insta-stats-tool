<?php
defined('ALTUMCODE') || die();
/* For YouTube source there is no proxy handling because it uses a direct API */

/* We need to check if the user already exists in our database */
$source_account = Database::get('*', 'twitter_users', ['username' => $user]);

if(!$source_account || ($source_account && (new \DateTime())->modify('-'.$settings->twitter_check_interval.' hours') > (new \DateTime($source_account->last_check_date)))) {

    $twitter_connection = new Abraham\TwitterOAuth\TwitterOAuth($settings->twitter_consumer_key, $settings->twitter_secret_key, $settings->twitter_oauth_token, $settings->twitter_oauth_token_secret);
    $twitter_verify = $twitter_connection->get('account/verify_credentials');

    /* Check for errors @ authentication */
    if(isset($twitter_verify->errors) && count($twitter_verify->errors) > 0) {
        foreach ($twitter_verify->errors as $twitter_error) {
            $_SESSION['error'][] = $twitter_error->message;
        }

        redirect();
    }

    /* Check if username completely matches the search */
    $source_account_data = $twitter_connection->get('users/lookup', ['screen_name' => $user]);

    if(isset($source_account_data->errors) && count($source_account_data->errors) > 0) {
        foreach ($source_account_data->errors as $twitter_error) {
            $_SESSION['error'][] = $twitter_error->message;
        }

        redirect();
    }

    /* Check if the account needs to be added and has more than needed followers */
    if(!$source_account) {
        if($source_account_data[0]->followers_count < $settings->twitter_minimum_followers) {
            $_SESSION['error'][] = sprintf($language->twitter->report->error_message->low_followers, $settings->twitter_minimum_followers);
        }

        if(!empty($_SESSION['error'])) redirect();
    }


    /* Check the account for tweets if wanted */
    if($settings->twitter_check_tweets) {
        $source_account_tweets = $twitter_connection->get('statuses/user_timeline', [
            'name' => $user,
            'screen_name' => $user,
            'include_rts' => false,
            'count' => $settings->twitter_check_tweets
        ]);

        if(isset($source_account_tweets->errors) && count($source_account_tweets->errors) > 0) {
            foreach ($source_account_data->errors as $twitter_error) {
                $_SESSION['error'][] = $twitter_error->message;
            }

            redirect();
        }
    }


    /* Vars to be added & used */
    $source_account_new = new StdClass();
    $source_account_new->twitter_id = $source_account_data[0]->id;
    $source_account_new->username = $source_account_data[0]->screen_name;
    $source_account_new->full_name = $source_account_data[0]->name;
    $source_account_new->description = $source_account_data[0]->description;
    $source_account_new->website = $source_account_data[0]->url;
    $source_account_new->followers = $source_account_data[0]->followers_count;
    $source_account_new->following = $source_account_data[0]->friends_count;
    $source_account_new->tweets = $source_account_data[0]->statuses_count;
    $source_account_new->likes = $source_account_data[0]->favourites_count;
    $source_account_new->profile_picture_url = str_replace('_normal', '_400x400', $source_account_data[0]->profile_image_url_https ?? '');
    $source_account_new->is_private = (int) $source_account_data[0]->protected;
    $source_account_new->is_verified = (int) $source_account_data[0]->verified;

    /* Get extra details from the response */
    $details = [
        'location'      => $source_account_data[0]->location,
        'color'         => $source_account_data[0]->profile_link_color ?? null,
        'created_date'  => isset($source_account_data[0]->created_at) ? (new \DateTime($source_account_data[0]->created_at))->format('Y-m-d H:i:s') : ''
    ];
    $details = json_encode($details);


    /* Insert / Update db */
    $stmt = $database->prepare("INSERT INTO `twitter_users` (
        `twitter_id`,
        `username`,
        `full_name`,
        `description`,
        `website`,
        `followers`,
        `following`,
        `tweets`,
        `likes`,
        `profile_picture_url`,
        `is_private`,
        `is_verified`,
        `details`,
        `added_date`,
        `last_check_date`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        `username` = VALUES (username),
        `full_name` = VALUES (full_name),
        `description` = VALUES (description),
        `website` = VALUES (website),
        `followers` = VALUES (followers),
        `following` = VALUES (following),
        `tweets` = VALUES (tweets),
        `likes` = VALUES (likes),
        `profile_picture_url` = VALUES (profile_picture_url),
        `is_private` = VALUES (is_private),
        `is_verified` = VALUES (is_verified),
        `details` = VALUES (details),
        `last_check_date` = VALUES (last_check_date)
    ");
    $stmt->bind_param('sssssssssssssss',
        $source_account_new->twitter_id,
        $source_account_new->username,
        $source_account_new->full_name,
        $source_account_new->description,
        $source_account_new->website,
        $source_account_new->followers,
        $source_account_new->following,
        $source_account_new->tweets,
        $source_account_new->likes,
        $source_account_new->profile_picture_url,
        $source_account_new->is_private,
        $source_account_new->is_verified,
        $details,
        $date,
        $date
    );
    $stmt->execute();
    $stmt->close();

    /* Retrieve the just created / updated row */
    $source_account = Database::get('*', 'twitter_users', ['twitter_id' => $source_account_new->twitter_id]);

    /* Update or insert the check log */
    $log = $database->query("SELECT `id` FROM `twitter_logs` WHERE `twitter_user_id` = '{$source_account->id}' AND DATEDIFF('{$date}', `date`) = 0")->fetch_object();

    if($log) {
        Database::update(
            'twitter_logs',
            [
                'followers'     => $source_account->followers,
                'following'     => $source_account->following,
                'tweets'        => $source_account->tweets,
                'likes'         => $source_account->likes,
                'date'          => $date
            ],
            ['id' => $log->id]
        );
    } else {
        $stmt = $database->prepare("INSERT INTO `twitter_logs` (
            `twitter_user_id`,
            `username`,
            `followers`,
            `following`,
            `tweets`,
            `likes`,
            `date`
        ) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss',
            $source_account->id,
            $source_account->username,
            $source_account->followers,
            $source_account->following,
            $source_account->tweets,
            $source_account->likes,
            $date
        );
        $stmt->execute();
        $stmt->close();
    }

    /* Update or insert the tweets */
    if($settings->twitter_check_tweets && $source_account_tweets) {

        /* Go over all the last videos and add them / update them in the database */
        foreach($source_account_tweets as $tweet) {
            $tweet_data = new StdClass();
            $tweet_data->tweet_id = $tweet->id;
            $tweet_data->text = $tweet->text;
            $tweet_data->source = $tweet->source;
            $tweet_data->language = $tweet->lang;
            $tweet_data->retweets = $tweet->retweet_count ?? 0;
            $tweet_data->likes = $tweet->favorite_count ?? 0;
            $tweet_data->details = json_encode([]);
            $tweet_data->created_date = (new \DateTime($tweet->created_at))->format('Y-m-d H:i:s');

            $stmt = $database->prepare("INSERT INTO `twitter_tweets` (
                `twitter_user_id`,
                `tweet_id`,
                `text`,
                `source`,
                `language`,
                `retweets`,
                `likes`,
                `details`,
                `created_date`,
                `date`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                `twitter_user_id` = VALUES (twitter_user_id),
                `text` = VALUES (text),
                `source` = VALUES (source),
                `language` = VALUES (language),
                `retweets` = VALUES (retweets),
                `likes` = VALUES (likes),
                `details` = VALUES (details),
                `created_date` = VALUES (created_date)
            ");
            $stmt->bind_param('ssssssssss',
                $source_account->id,
                $tweet_data->tweet_id,
                $tweet_data->text,
                $tweet_data->source,
                $tweet_data->language,
                $tweet_data->retweets,
                $tweet_data->likes,
                $tweet_data->details,
                $tweet_data->created_date,
                $date
            );
            $stmt->execute();
            $stmt->close();
        }

    }
}

/* Retrieve last X entries */
$logs = [];

if($date_start && $date_end) {
    $date_start_query = (new DateTime($date_start))->format('Y-m-d H:i:s');
    $date_end_query = (new DateTime($date_end))->modify('+1 day')->format('Y-m-d H:i:s');

    $logs_result = $database->query("SELECT * FROM `twitter_logs` WHERE `twitter_user_id` = '{$source_account->id}' AND (`date` BETWEEN '{$date_start_query}' AND '{$date_end_query}')  ORDER BY `date` DESC");
} else {
    $logs_result = $database->query("SELECT * FROM `twitter_logs` WHERE `twitter_user_id` = '{$source_account->id}' ORDER BY `date` DESC LIMIT 15");
}


while($log = $logs_result->fetch_assoc()) { $logs[] = $log; }
$logs = array_reverse($logs);

/* Generate data for the charts and retrieving the average followers /uploads per day */
$logs_chart = [
    'labels'        => [],
    'followers'     => [],
    'following'     => [],
    'tweets'        => [],
    'likes'         => []
];

$total_new = [
    'followers'     => [],
    'following'     => [],
    'tweets'        => [],
    'likes'         => []
];

for($i = 0; $i < count($logs); $i++) {
    $logs_chart['labels'][] = (new \DateTime($logs[$i]['date']))->format($language->global->date->datetime_format);
    $logs_chart['followers'][] = $logs[$i]['followers'];
    $logs_chart['following'][] = $logs[$i]['following'];
    $logs_chart['tweets'][] = $logs[$i]['tweets'];
    $logs_chart['likes'][] = $logs[$i]['likes'];

    if($i != 0) {
        $total_new['followers'][] = $logs[$i]['followers'] - $logs[$i - 1]['followers'];
        $total_new['following'][] = $logs[$i]['following'] - $logs[$i - 1]['following'];
        $total_new['tweets'][] = $logs[$i]['tweets'] - $logs[$i - 1]['tweets'];
        $total_new['likes'][] = $logs[$i]['likes'] - $logs[$i - 1]['likes'];
    }
}

/* reverse it back */
$logs = array_reverse($logs);

/* Defining the chart data */
$logs_chart = generate_chart_data($logs_chart);

/* Defining the future projections data */
$total_days = count($logs) > 1 ? (new \DateTime($logs[count($logs)-1]['date']))->diff((new \DateTime($logs[1]['date'])))->format('%a') : 0;

$average = [
    'followers'   => $total_days > 0 ? (int) ceil(array_sum($total_new['followers']) / $total_days) : 0,
    'following'   => $total_days > 0 ? (int) ceil(array_sum($total_new['following']) / $total_days) : 0,
    'tweets'      => $total_days > 0 ? (int) ceil(array_sum($total_new['tweets']) / $total_days) : 0,
    'likes'       => $total_days > 0 ? (int) ceil(array_sum($total_new['likes']) / $total_days) : 0,
];

$source_account->details = json_decode($source_account->details);

/* Get the account tweets if enabled */
if($settings->twitter_check_tweets) {
    $source_account_tweets_result = $database->query("SELECT * FROM `twitter_tweets` WHERE `twitter_user_id` = {$source_account->id} ORDER BY `created_date` DESC LIMIT {$settings->twitter_check_tweets}");
    $tweets_result = [];

    $tweets_chart = [
        'labels'    => [],
        'retweets'  => [],
        'likes'     => [],
    ];

    /* Iterating and storing proper data for charts and later use */
    while($row = $source_account_tweets_result->fetch_object()) { $tweets_result[] = $row; }

    $tweets_result = array_reverse($tweets_result);

    for($i = 0; $i < count($tweets_result); $i++) {
        $tweets_chart['labels'][] = (new \DateTime($tweets_result[$i]->created_date))->format($language->global->date->datetime_format);
        $tweets_chart['retweets'][] = $tweets_result[$i]->retweets;
        $tweets_chart['likes'][] = $tweets_result[$i]->likes;
    }

    $tweets_result = array_reverse($tweets_result);

    /* Defining the chart data for media */
    $tweets_chart = generate_chart_data($tweets_chart);

}

/* Custom title */
add_event('title', function() {
    global $page_title;
    global $source_account;
    global $language;

    $page_title = sprintf($language->twitter->report->title, $source_account->full_name);
});

