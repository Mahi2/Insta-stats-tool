<?php
defined('ALTUMCODE') || die();

/* We need to get the next users that we are going to check */
if($settings->store_unlock_report_price == '0' || ($settings->store_unlock_report_price != '0' && $settings->cron_mode == 'ALL')) {
    $result = $database->query("
        SELECT `username`, `full_name`, `last_check_date`, `id`
        FROM `instagram_users`
        WHERE TIMESTAMPDIFF(HOUR, `last_check_date`, '{$date}') > {$settings->instagram_check_interval} 
        ORDER BY `last_check_date` ASC
        LIMIT {$settings->cron_queries}
    ");
} else if($settings->store_unlock_report_price != '0') {
    $result = $database->query("
        SELECT  `unlocked_reports`.`date`, `unlocked_reports`.`expiration_date`, `instagram_users`.`username`, `instagram_users`.`full_name`, `instagram_users`.`last_check_date`, `instagram_users`.`id`
        FROM `unlocked_reports` 
        LEFT JOIN `instagram_users` ON `unlocked_reports`.`source_user_id` = `instagram_users`.`id` 
        WHERE TIMESTAMPDIFF(HOUR, `instagram_users`.`last_check_date`, '{$date}') > {$settings->instagram_check_interval}
        ORDER BY `instagram_users`.`last_check_date` ASC
        LIMIT {$settings->cron_queries}
    ");
}


/* Iterate through the results */
while($source_account = $result->fetch_object()) {
    if(DEBUG) { echo 'Instagram Account Check: '; print_r($source_account); echo '<br />'; }

    $user = $source_account->username;

    $instagram = new \InstagramScraper\Instagram();
    $instagram->setUserAgent(get_random_user_agent());

    /* Set proxy if needed */
    if($is_proxy_request) {
        $instagram::setProxy($is_proxy_request);
    }

    try {
        $source_account_data = $instagram->getAccount($user);
    } catch (Exception $error) {
        $error_message = $error->getMessage();

        /* Make sure to set the failed request to the proxy */
        if($is_proxy_request) {
            if($error->getCode() == 429) {
                $database->query("UPDATE `proxies` SET `failed_requests` = `failed_requests` + 1, `total_failed_requests` = `total_failed_requests` + 1, `last_date` = '{$date}' WHERE `proxy_id` = {$proxy->proxy_id}");
            } else {
                $database->query("UPDATE `proxies` SET `last_date` = '{$date}' WHERE `proxy_id` = {$proxy->proxy_id}");
            }
        }

        /* Update the user so it will not get checked again until it's time comes */
        Database::update(
            'instagram_users',
            ['last_check_date' => $date],
            ['id' => $source_account->id]
        );

        if(DEBUG) { echo 'Something happened, error:'; print_r($error_message); echo '<br />'; }

        /* If the account is not existing anymore, remove it */
        if($error->getCode() == 404) {

            $database->query("DELETE FROM `instagram_users` WHERE `id` = '{$source_account->id}'");
            $database->query("DELETE FROM `favorites` WHERE `source_user_id` = '{$source_account->id}' AND `source` = 'INSTAGRAM'");
            $database->query("DELETE FROM `email_reports` WHERE `source_user_id` = '{$source_account->id}' AND `source` = 'INSTAGRAM'");

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
    $source_account_new->instagram_id = $source_account_data->getId();
    $source_account_new->username = $source_account_data->getUsername();
    $source_account_new->full_name = $source_account_data->getFullName();
    $source_account_new->description = $source_account_data->getBiography();
    $source_account_new->website = $source_account_data->getExternalUrl();
    $source_account_new->followers = $source_account_data->getFollowedByCount();
    $source_account_new->following = $source_account_data->getFollowsCount();
    $source_account_new->uploads = $source_account_data->getMediaCount();
    $source_account_new->profile_picture_url = $source_account_data->getProfilePicUrl();
    $source_account_new->is_private = (int) $source_account_data->isPrivate();
    $source_account_new->is_verified = (int) $source_account_data->isVerified();


    if($source_account_new->is_private) {
        $source_account_new->average_engagement_rate = 0;
        $details = '';
    }

    else {
        $media_response = $instagram->getPaginateMedias($user, '', $source_account_data);

        /* Get extra details from last media */
        $likes_array = [];
        $comments_array = [];
        $engagement_rate_array = [];
        $hashtags_array = [];
        $mentions_array = [];
        $top_posts_array = [];
        $details = [];

        /* Go over each recent media post */
        foreach ($media_response['medias'] as $media) {
            $likes_array[$media->getShortCode()] = $media->getLikesCount();
            $comments_array[$media->getShortCode()] = $media->getCommentsCount();
            $engagement_rate_array[$media->getShortCode()] = nr(($media->getLikesCount() + $media->getCommentsCount()) / $source_account_new->followers * 100, 2);

            $hashtags = InstagramHelper::get_hashtags($media->getCaption());

            foreach ($hashtags as $hashtag) {
                if(!isset($hashtags_array[$hashtag])) {
                    $hashtags_array[$hashtag] = 1;
                } else {
                    $hashtags_array[$hashtag]++;
                }
            }

            $mentions = InstagramHelper::get_mentions($media->getCaption());

            foreach ($mentions as $mention) {
                if(!isset($mentions_array[$mention])) {
                    $mentions_array[$mention] = 1;
                } else {
                    $mentions_array[$mention]++;
                }
            }

            /* Getting the data for the insertion in the database */
            $media_data = new StdClass();
            $media_data->media_id = $media->getId();
            $media_data->shortcode = $media->getShortCode();
            $media_data->created_date = $media->getCreatedTime();
            $media_data->caption = $media->getCaption();
            $media_data->comments = $media->getCommentsCount();
            $media_data->likes = $media->getLikesCount();
            $media_data->media_url = $media->getLink();
            $media_data->media_image_url = $media->getImageHighResolutionUrl();
            $media_data->type = strtoupper($media->getType());
            $media_data->mentions = json_encode($mentions);
            $media_data->hashtags = json_encode($hashtags);

            /* Make sure to delete old instagram media records */
            $database->query("DELETE FROM `instagram_media` WHERE `instagram_user_id` = '{$source_account->id}' AND `date` < NOW() - INTERVAL 30 DAY");


            $stmt = $database->prepare("INSERT INTO `instagram_media` (
                `media_id`,
                `instagram_user_id`,
                `shortcode`,
                `created_date`,
                `caption`,
                `comments`,
                `likes`,
                `media_url`,
                `media_image_url`,
                `type`,
                `mentions`,
                `hashtags`,
                `date`,
                `last_check_date`
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
              ON DUPLICATE KEY UPDATE
                `instagram_user_id` = VALUES (instagram_user_id),
                `shortcode` = VALUES (shortcode),
                `created_date` = VALUES (created_date),
                `caption` = VALUES (caption),
                `comments` = VALUES (comments),
                `likes` = VALUES (likes),
                `media_url` = VALUES (media_url),
                `media_image_url` = VALUES (media_image_url),
                `type` = VALUES (type),
                `mentions` = VALUES (mentions),
                `hashtags` = VALUES (hashtags),
                `last_check_date` = VALUES (last_check_date)
            ");
            $stmt->bind_param('ssssssssssssss',
                $media_data->media_id,
                $source_account->id,
                $media_data->shortcode,
                $media_data->created_date,
                $media_data->caption,
                $media_data->comments,
                $media_data->likes,
                $media_data->media_url,
                $media_data->media_image_url,
                $media_data->type,
                $media_data->mentions,
                $media_data->hashtags,
                $date,
                $date
            );
            $stmt->execute();

            if(count($likes_array) >= $settings->instagram_calculator_media_count) break;
        }

        /* Calculate needed details */
        $details['total_likes'] = array_sum($likes_array);
        $details['total_comments'] = array_sum($comments_array);
        $details['average_comments'] = count($likes_array) > 0 ? $details['total_comments'] / count($comments_array) : 0;
        $details['average_likes'] = count($likes_array) > 0 ? $details['total_likes'] / count($likes_array) : 0;
        $source_account_new->average_engagement_rate = count($likes_array) > 0 ? number_format(array_sum($engagement_rate_array) / count($engagement_rate_array), 2) : 0;


        /* Do proper sorting */
        arsort($engagement_rate_array);
        arsort($hashtags_array);
        arsort($mentions_array);
        $top_posts_array = array_slice($engagement_rate_array, 0, 3);
        $top_hashtags_array = array_slice($hashtags_array, 0, 15);
        $top_mentions_array = array_slice($mentions_array, 0, 15);

        /* Get them all together */
        $details['top_hashtags'] = $top_hashtags_array;
        $details['top_mentions'] = $top_mentions_array;
        $details['top_posts'] = $top_posts_array;
        $details = json_encode($details);

    }


    /* Update the user main data */
    $stmt = $database->prepare("UPDATE `instagram_users` SET
        `instagram_id` = ?,
        `full_name` = ?,
        `description`= ?,
        `website`= ?,
        `followers`= ?,
        `following`= ?,
        `uploads`= ?,
        `average_engagement_rate` = ?,
        `details` = ?,
        `profile_picture_url`= ?,
        `is_private`= ?,
        `is_verified`= ?,
        `last_check_date` = ?,
        `last_successful_check_date` = ?

        WHERE `id` = ?
    ");
    $stmt->bind_param('sssssssssssssss',
        $source_account_new->instagram_id,
        $source_account_new->full_name,
        $source_account_new->description,
        $source_account_new->website,
        $source_account_new->followers,
        $source_account_new->following,
        $source_account_new->uploads,
        $source_account_new->average_engagement_rate,
        $details,
        $source_account_new->profile_picture_url,
        $source_account_new->is_private,
        $source_account_new->is_verified,
        $date,
        $date,
        $source_account->id
    );
    $stmt->execute();
    $stmt->close();


    /* Retrieve the just created / updated row */
    $source_account = Database::get('*', 'instagram_users', ['id' => $source_account->id]);

    /* Get the current day log */
    $log = $database->query("SELECT * FROM `instagram_logs` WHERE `instagram_user_id` = '{$source_account->id}' ORDER BY `id` DESC")->fetch_object();

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
                    'uploads' => floor($log->uploads + $i * (($source_account->uploads - $log->uploads) / $days_difference)),
                    'average_engagement_rate' => number_format($log->average_engagement_rate + $i * (($source_account->average_engagement_rate - $log->average_engagement_rate) / $days_difference), 2, '.', ''),
                    'date' => (new DateTime($log->date))->modify('+' . $i . ' days')->format('Y-m-d H:i:s')
                ];

                $stmt = $database->prepare("INSERT INTO `instagram_logs` (
                    `instagram_user_id`,
                    `username`,
                    `followers`,
                    `following`,
                    `uploads`,
                    `average_engagement_rate`,
                    `date`
                ) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('sssssss',
                    $source_account->id,
                    $source_account->username,
                    $spread['followers'],
                    $spread['following'],
                    $spread['uploads'],
                    $spread['average_engagement_rate'],
                    $spread['date']
                );
                $stmt->execute();
                $stmt->close();

                if(DEBUG) { echo 'Inserted in between log ' . print_r($spread) . ' <br /><br />'; }

            }

        }

        if($days_difference == 0) {

            Database::update(
                'instagram_logs',
                [
                    'followers' => $source_account->followers,
                    'following' => $source_account->following,
                    'uploads' => $source_account->uploads,
                    'average_engagement_rate' => $source_account->average_engagement_rate,
                    'date' => $date
                ],
                ['id' => $log->id]
            );

        }
    }

    /* If no log is existing in the current day, insert it  */
    if(!$log || ($log && $days_difference > 0)) {

        $stmt = $database->prepare("INSERT INTO `instagram_logs` (
            `instagram_user_id`,
            `username`,
            `followers`,
            `following`,
            `uploads`,
            `average_engagement_rate`,
            `date`
        ) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss',
            $source_account->id,
            $source_account->username,
            $source_account->followers,
            $source_account->following,
            $source_account->uploads,
            $source_account->average_engagement_rate,
            $date
        );
        $stmt->execute();
        $stmt->close();

    }

}
