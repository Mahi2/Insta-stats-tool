<?php
defined('ALTUMCODE') || die();

/* We need to get the next users that we are going to check */
if($settings->store_unlock_report_price == '0' || ($settings->store_unlock_report_price != '0' && $settings->cron_mode == 'ALL')) {
    $result = $database->query("
        SELECT `twitter_id`, `username`, `last_check_date`, `id`
        FROM `twitter_users`
        WHERE TIMESTAMPDIFF(HOUR, `last_check_date`, '{$date}') > {$settings->twitter_check_interval} 
        ORDER BY `last_check_date` ASC
        LIMIT {$settings->cron_queries}
    ");
} else if($settings->store_unlock_report_price != '0') {
    $result = $database->query("
        SELECT  `unlocked_reports`.`date`, `unlocked_reports`.`expiration_date`, `twitter_users`.`twitter_id`, `twitter_users`.`username`, `twitter_users`.`last_check_date`, `twitter_users`.`id`
        FROM `unlocked_reports` 
        LEFT JOIN `twitter_users` ON `unlocked_reports`.`source_user_id` = `twitter_users`.`id` 
        WHERE TIMESTAMPDIFF(HOUR, `twitter_users`.`last_check_date`, '{$date}') > {$settings->twitter_check_interval}
        ORDER BY `twitter_users`.`last_check_date` ASC
        LIMIT {$settings->cron_queries}
    ");
}


/* Iterate through the results */
while($source_account = $result->fetch_object()) {
    if(DEBUG) { echo 'Twitter Account Check: '; print_r($source_account); echo '<br />'; }

    $user = $source_account->username;

    $twitter_connection = new Abraham\TwitterOAuth\TwitterOAuth($settings->twitter_consumer_key, $settings->twitter_secret_key, $settings->twitter_oauth_token, $settings->twitter_oauth_token_secret);
    $twitter_verify = $twitter_connection->get('account/verify_credentials');

    /* Check for errors @ authentication */
    if(isset($twitter_verify->errors) && count($twitter_verify->errors) > 0) {
        foreach ($twitter_verify->errors as $twitter_error) {
            if(DEBUG) { echo 'Something happened, error:'; print_r($twitter_error->message); echo '<br />'; }

            break 2;
        }
    }

    /* Check if username completely matches the search */
    $source_account_data = $twitter_connection->get('users/lookup', ['screen_name' => $user]);

    if(isset($source_account_data->errors) && count($source_account_data->errors) > 0) {
        foreach ($source_account_data->errors as $twitter_error) {

            /* Update the user so it will not get checked again until it's time comes */
            Database::update(
                'twitter_users',
                ['last_check_date' => $date],
                ['id' => $source_account->id]
            );

            if(DEBUG) { echo 'Something happened, error:'; print_r($twitter_error->message); echo '<br />'; }

            /* If the account is not existing anymore, remove it */
            if(!$twitter_error->code == 17) {
                $database->query("DELETE FROM `twitter_users` WHERE `id` = '{$source_account->id}'");
                $database->query("DELETE FROM `favorites` WHERE `source_user_id` = '{$source_account->id}' AND `source` = 'YOUTUBE'");
                $database->query("DELETE FROM `email_reports` WHERE `source_user_id` = '{$source_account->id}' AND `source` = 'YOUTUBE'");

                if(DEBUG) { echo 'User ' . $source_account->twitter_id . ' was deleted from the database beacause it does not exist anymore'; echo '<br />'; }

                continue 2;
            }
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
    $source_account_new->profile_picture_url = $source_account_data[0]->profile_image_url_https ?? '';
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
        `last_check_date`,
        `last_successful_check_date`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
    $stmt->bind_param('ssssssssssssssss',
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
        $date,
        $date
    );
    $stmt->execute();
    $stmt->close();

    /* Retrieve the just created / updated row */
    $source_account = Database::get('*', 'twitter_users', ['id' => $source_account->id]);

    /* Get the current day log */
    $log = $database->query("SELECT * FROM `twitter_logs` WHERE `twitter_user_id` = '{$source_account->id}' ORDER BY `id` DESC")->fetch_object();

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
                    'following' => floor($log->following + $i * (($source_account->following - $log->following) / $days_difference)),
                    'tweets' => floor($log->tweets + $i * (($source_account->tweets - $log->tweets) / $days_difference)),
                    'likes' => floor($log->likes + $i * (($source_account->likes - $log->likes) / $days_difference)),
                    'date' => (new DateTime($log->date))->modify('+' . $i . ' days')->format('Y-m-d H:i:s')
                ];

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
                    $spread['followers'],
                    $spread['following'],
                    $spread['tweets'],
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
                'twitter_logs',
                [
                    'followers' => $source_account->followers,
                    'following' => $source_account->following,
                    'tweets' => $source_account->tweets,
                    'likes' => $source_account->likes,
                    'date' => $date
                ],
                ['id' => $log->id]
            );

        }
    }

    /* If no log is existing in the current day, insert it  */
    if(!$log || ($log && $days_difference > 0)) {

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

}
