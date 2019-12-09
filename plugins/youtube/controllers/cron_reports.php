<?php
defined('ALTUMCODE') || die();

/* We need to get the next users that we are going to check */
if($settings->store_unlock_report_price == '0' || ($settings->store_unlock_report_price != '0' && $settings->cron_mode == 'ALL')) {
    $result = $database->query("
        SELECT `youtube_id`, `title`, `last_check_date`, `id`, `uploads_playlist_id`
        FROM `youtube_users`
        WHERE TIMESTAMPDIFF(HOUR, `last_check_date`, '{$date}') > {$settings->youtube_check_interval} 
        ORDER BY `last_check_date` ASC
        LIMIT {$settings->cron_queries}
    ");
} else if($settings->store_unlock_report_price != '0') {
    $result = $database->query("
        SELECT  `unlocked_reports`.`date`, `unlocked_reports`.`expiration_date`, `youtube_users`.`uploads_playlist_id`, `youtube_users`.`youtube_id`, `youtube_users`.`title`, `youtube_users`.`last_check_date`, `youtube_users`.`id`
        FROM `unlocked_reports` 
        LEFT JOIN `youtube_users` ON `unlocked_reports`.`source_user_id` = `youtube_users`.`id` 
        WHERE TIMESTAMPDIFF(HOUR, `youtube_users`.`last_check_date`, '{$date}') > {$settings->youtube_check_interval}
        ORDER BY `youtube_users`.`last_check_date` ASC
        LIMIT {$settings->cron_queries}
    ");
}


/* Iterate through the results */
while($source_account = $result->fetch_object()) {
    if(DEBUG) { echo 'YouTube Account Check: '; print_r($source_account); echo '<br />'; }

    $youtube = new YouTube();

    /* Set the needed parameters */
    $youtube->set_header_referer($settings->url);
    $youtube->set_type('channels');
    $youtube->set_parameters(['key' => $settings->youtube_api_key]);
    $youtube->set_parameters(['id' => $source_account->youtube_id]);

    /* Check if the user already has the playlist id of the uploads set */
    if(!empty($source_account->uploads_playlist_id)) {
        $youtube->set_parameters(['part' => 'snippet,statistics']);
    } else {
        /* If no uploads playlist id is found, then we need to get it */
        $youtube->set_parameters(['part' => 'snippet,statistics,contentDetails']);
    }

    try {
        $source_account_data = $youtube->get();

    } catch (Exception $error) {
        $error_message = $error->getMessage();

        /* Update the user so it will not get checked again until it's time comes */
        Database::update(
            'youtube_users',
            ['last_check_date' => $date],
            ['id' => $source_account->id]
        );

        if(DEBUG) { echo 'Something happened, error:'; print_r($error_message); echo '<br />'; }

        continue;
    }

    /* If the account is not existing anymore, remove it */
    if(!$source_account_data) {

        $database->query("DELETE FROM `youtube_users` WHERE `id` = '{$source_account->id}'");
        $database->query("DELETE FROM `favorites` WHERE `source_user_id` = '{$source_account->id}' AND `source` = 'YOUTUBE'");
        $database->query("DELETE FROM `email_reports` WHERE `source_user_id` = '{$source_account->id}' AND `source` = 'YOUTUBE'");

        if(DEBUG) { echo 'User ' . $source_account->youtube_id . ' was deleted from the database beacause it does not exist anymore'; echo '<br />'; }

        continue;
    }


    /* Vars to be added & used */
    $source_account_new = new StdClass();
    $source_account_new->youtube_id = $source_account_data[0]->id;
    $source_account_new->username = $source_account_data[0]->snippet->customUrl;
    $source_account_new->title = $source_account_data[0]->snippet->title;
    $source_account_new->description = $source_account_data[0]->snippet->description;
    $source_account_new->subscribers = $source_account_data[0]->statistics->subscriberCount;
    $source_account_new->views = $source_account_data[0]->statistics->viewCount;
    $source_account_new->videos = $source_account_data[0]->statistics->videoCount;
    $source_account_new->profile_picture_url = $source_account_data[0]->snippet->thumbnails->high->url ?? '';
    $source_account_new->uploads_playlist_id = !empty($source_account->uploads_playlist_id) ? $source_account->uploads_playlist_id : $source_account_data[0]->contentDetails->relatedPlaylists->uploads;

    /* Get extra details from the response */
    $details = [
        'country'       => $source_account_data[0]->snippet->country ?? null,
        'created_date'  => isset($source_account_data[0]->snippet->publishedAt) ? (new \DateTime($source_account_data[0]->snippet->publishedAt))->format('Y-m-d H:i:s') : ''
    ];
    $details = json_encode($details);


    /* Update the user main data */
    $stmt = $database->prepare("UPDATE `youtube_users` SET
        `title` = ?,
        `subscribers` = ?,
        `views` = ?,
        `videos` = ?,
        `details` = ?,
        `profile_picture_url` = ?,
        `last_check_date` = ?,
        `last_successful_check_date` = ?

        WHERE `id` = ?
    ");
    $stmt->bind_param('sssssssss',
        $source_account_new->title,
        $source_account_new->subscribers,
        $source_account_new->views,
        $source_account_new->videos,
        $details,
        $source_account_new->profile_picture_url,
        $date,
        $date,
        $source_account->id
    );
    $stmt->execute();
    $stmt->close();

    /* Update the playlist uploads id if empty */
    if(empty($source_account->uploads_playlist_id)) {
        $stmt = $database->prepare("UPDATE `youtube_users` SET  `uploads_playlist_id` = ? WHERE `id` = ?");
        $stmt->bind_param('ss',
            $source_account_new->uploads_playlist_id,
            $source_account->id
        );
        $stmt->execute();
        $stmt->close();
    }

    /* Retrieve the just created / updated row */
    $source_account = Database::get('*', 'youtube_users', ['id' => $source_account->id]);

    /* Get the current day log */
    $log = $database->query("SELECT * FROM `youtube_logs` WHERE `youtube_user_id` = '{$source_account->id}' ORDER BY `id` DESC")->fetch_object();

    if($log) {
        $current_date = (new \DateTime())->format('Y-m-d');
        $days_difference = (new \DateTime((new \DateTime($log->date))->format('Y-m-d')))->diff((new \DateTime($current_date)))->format('%a');

        /* Try to auto calculate the missing days in between the last log and today */
        if($days_difference > 0 && $settings->cron_auto_add_missing_logs) {

            /* Insert in between logs */
            for($i = 1; $i < $days_difference; $i++) {
                $is_generated = 1;

                $spread = [
                    'subscribers' => floor($log->subscribers + $i * (($source_account->subscribers - $log->subscribers) / $days_difference)),
                    'views' => floor($log->views + $i * (($source_account->views - $log->views) / $days_difference)),
                    'videos' => floor($log->videos + $i * (($source_account->videos - $log->videos) / $days_difference)),
                    'date' => (new DateTime($log->date))->modify('+' . $i . ' days')->format('Y-m-d H:i:s')
                ];

                $stmt = $database->prepare("INSERT INTO `youtube_logs` (
                    `youtube_user_id`,
                    `youtube_id`,
                    `subscribers`,
                    `views`,
                    `videos`,
                    `date`
                ) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssss',
                    $source_account->id,
                    $source_account->youtube_id,
                    $spread['subscribers'],
                    $spread['views'],
                    $spread['videos'],
                    $spread['date']
                );
                $stmt->execute();
                $stmt->close();

                if(DEBUG) { echo 'Inserted in between log ' . print_r($spread) . ' <br /><br />'; }

            }

        }

        if($days_difference == 0) {

            Database::update(
                'youtube_logs',
                [
                    'subscribers' => $source_account->subscribers,
                    'views' => $source_account->views,
                    'videos' => $source_account->videos,
                    'date' => $date
                ],
                ['id' => $log->id]
            );

        }
    }

    /* If no log is existing in the current day, insert it  */
    if(!$log || ($log && $days_difference > 0)) {

        $stmt = $database->prepare("INSERT INTO `youtube_logs` (
            `youtube_user_id`,
            `youtube_id`,
            `subscribers`,
            `views`,
            `videos`,
            `date`
        ) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss',
            $source_account->id,
            $source_account->youtube_id,
            $source_account->subscribers,
            $source_account->views,
            $source_account->videos,
            $date
        );
        $stmt->execute();
        $stmt->close();

    }

    /* Send a new request to get the last uploaded items */
    if($settings->youtube_check_videos && !empty($source_account->uploads_playlist_id)) {
        $youtube->set_type('playlistItems');
        $youtube->set_parameters(['part' => 'snippet']);
        $youtube->set_parameters(['playlistId' => $source_account->uploads_playlist_id]);
        $youtube->set_parameters(['id' => false]);
        $youtube->set_parameters(['maxResults' => $settings->youtube_check_videos]);
        $uploads_list = $youtube->get();

        $youtube_video_ids = implode(',', array_map(function ($item) {
            return $item->snippet->resourceId->videoId;
        }, $uploads_list));

        /* Get details about videos */
        $youtube->set_type('videos');
        $youtube->set_parameters(['part' => 'snippet,statistics']);
        $youtube->set_parameters(['playlistId' => false]);
        $youtube->set_parameters(['id' => $youtube_video_ids]);
        $source_account_videos = $youtube->get();

        /* Go over all the last videos and add them / update them in the database */
        foreach($source_account_videos as $video) {
            $video_data = new StdClass();
            $video_data->video_id = $video->id;
            $video_data->title = $video->snippet->title;
            $video_data->description = $video->snippet->description;
            $video_data->views = $video->statistics->viewCount ?? 0;
            $video_data->likes = $video->statistics->likeCount ?? 0;
            $video_data->dislikes = $video->statistics->dislikeCount ?? 0;
            $video_data->comments = $video->statistics->commentCount ?? 0;
            $video_data->details = json_encode([]);
            $video_data->thumbnail_url = $video->snippet->thumbnails->default->url;
            $video_data->created_date = (new \DateTime($video->snippet->publishedAt))->format('Y-m-d H:i:s');

            $stmt = $database->prepare("INSERT INTO `youtube_videos` (
                `youtube_user_id`,
                `video_id`,
                `title`,
                `description`,
                `views`,
                `likes`,
                `dislikes`,
                `comments`,
                `thumbnail_url`,
                `details`,
                `created_date`,
                `date`
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                `youtube_user_id` = VALUES (youtube_user_id),
                `title` = VALUES (title),
                `description` = VALUES (description),
                `views` = VALUES (views),
                `likes` = VALUES (likes),
                `dislikes` = VALUES (dislikes),
                `comments` = VALUES (comments),
                `thumbnail_url` = VALUES (thumbnail_url),
                `details` = VALUES (details),
                `created_date` = VALUES (created_date)
            ");
            $stmt->bind_param('ssssssssssss',
                $source_account->id,
                $video_data->video_id,
                $video_data->title,
                $video_data->description,
                $video_data->views,
                $video_data->likes,
                $video_data->dislikes,
                $video_data->comments,
                $video_data->thumbnail_url,
                $video_data->details,
                $video_data->created_date,
                $date
            );
            $stmt->execute();
            $stmt->close();
        }
    }

}
