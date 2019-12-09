<?php
defined('ALTUMCODE') || die();

/* We need to check if the user already exists in our database */
$source_account = Database::get('*', 'instagram_users', ['username' => $user]);

if($refresh || !$source_account || ($source_account && (new \DateTime())->modify('-'.$settings->instagram_check_interval.' hours') > (new \DateTime($source_account->last_check_date)))) {
    $request_is_successful = true;
    $instagram = new \InstagramScraper\Instagram();
    $instagram->setUserAgent(get_random_user_agent());

    /* Set proxy if needed */
    if($is_proxy_request) {
        $instagram::setProxy($is_proxy_request);
    }

    try {
        $source_account_data = $instagram->getAccount($user);
    } catch (Exception $error) {

        /* Make sure to set the failed request to the proxy */
        if($is_proxy_request) {
            if($error->getCode() == 429) {
                $database->query("UPDATE `proxies` SET `failed_requests` = `failed_requests` + 1, `total_failed_requests` = `total_failed_requests` + 1, `last_date` = '{$date}' WHERE `proxy_id` = {$proxy->proxy_id}");
            } else {
                $database->query("UPDATE `proxies` SET `last_date` = '{$date}' WHERE `proxy_id` = {$proxy->proxy_id}");
            }
        }

        /* Redirect if the account does not have any data */
        if(!$source_account) {
            $_SESSION['error'][] = $error->getCode() == 404 ? $language->instagram->report->error_message->not_found : $error->getMessage();

            redirect();
        }

        /* Set the request as unsuccessful and set the last check date */
        $database->query("UPDATE `instagram_users` SET `last_check_date` = '{$date}' WHERE `id` = {$source_account->id}");
        $request_is_successful = false;
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
        if($source_account_data->getFollowedByCount() < $settings->instagram_minimum_followers) {
            $_SESSION['error'][] = sprintf($language->instagram->report->error_message->low_followers, $settings->instagram_minimum_followers);
        }

        if(!empty($_SESSION['error'])) redirect();
    }

    if($request_is_successful) {

        /* Vars to be added & used */
        $source_account_new = new StdClass();
        $source_account_new->instagram_id = $source_account_data->getId();
        $source_account_new->username = $source_account_data->getUsername();
        $source_account_new->full_name = $source_account_data->getFullName() != '' ? $source_account_data->getFullName() : $source_account_new->username;
        $source_account_new->description = $source_account_data->getBiography();
        $source_account_new->website = $source_account_data->getExternalUrl();
        $source_account_new->followers = $source_account_data->getFollowedByCount();
        $source_account_new->following = $source_account_data->getFollowsCount();
        $source_account_new->uploads = $source_account_data->getMediaCount();
        $source_account_new->profile_picture_url = $source_account_data->getProfilePicUrl();
        $source_account_new->is_private = (int)$source_account_data->isPrivate();
        $source_account_new->is_verified = (int) $source_account_data->isVerified();


        if($source_account_new->is_private) {
            $source_account_new->average_engagement_rate = 0;
            $details = '';
        } else {
            try {
                $media_response = $instagram->getPaginateMedias($user, '', $source_account_data);
            } catch (Exception $error) {
                $error_message = $_SESSION['error'][] = $error->getMessage();

                redirect();
            }

            /* Get extra details from last media */
            $likes_array = [];
            $comments_array = [];
            $engagement_rate_array = [];
            $hashtags_array = [];
            $mentions_array = [];
            $top_posts_array = [];
            $details = [];

            /* Go over each recent media post to generate stats */
            if ($media_response && !empty($media_response)) {
                foreach ($media_response['medias'] as $media) {

                    $likes_array[$media->getShortCode()] = $media->getLikesCount();
                    $comments_array[$media->getShortCode()] = $media->getCommentsCount();
                    $engagement_rate_array[$media->getShortCode()] = nr(($media->getLikesCount() + $media->getCommentsCount()) / $source_account_new->followers * 100, 2);

                    $hashtags = InstagramHelper::get_hashtags($media->getCaption());

                    foreach ($hashtags as $hashtag) {
                        if (!isset($hashtags_array[$hashtag])) {
                            $hashtags_array[$hashtag] = 1;
                        } else {
                            $hashtags_array[$hashtag]++;
                        }
                    }

                    $mentions = InstagramHelper::get_mentions($media->getCaption());

                    foreach ($mentions as $mention) {
                        if (!isset($mentions_array[$mention])) {
                            $mentions_array[$mention] = 1;
                        } else {
                            $mentions_array[$mention]++;
                        }
                    }

                    /* End if needed */
                    if (count($likes_array) >= $settings->instagram_calculator_media_count) break;
                }
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

        /* Insert into db */
        $stmt = $database->prepare("INSERT INTO `instagram_users` (
            `instagram_id`,
            `username`,
            `full_name`,
            `description`,
            `website`,
            `followers`,
            `following`,
            `uploads`,
            `average_engagement_rate`,
            `details`,
            `profile_picture_url`,
            `is_private`,
            `is_verified`,
            `added_date`,
            `last_check_date`,
            `last_successful_check_date`
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            `instagram_id` = VALUES (instagram_id),
            `username` = VALUES (username),
            `full_name` = VALUES (full_name),
            `description` = VALUES (description),
            `website` = VALUES (website),
            `followers` = VALUES (followers),
            `following` = VALUES (following),
            `uploads` = VALUES (uploads),
            `average_engagement_rate` = VALUES (average_engagement_rate),
            `details` = VALUES (details),
            `profile_picture_url` = VALUES (profile_picture_url),
            `is_private` = VALUES (is_private),
            `is_verified` = VALUES (is_verified),
            `last_check_date` = VALUES (last_check_date),
            `last_successful_check_date` = VALUES (last_successful_check_date)
        ");
        $stmt->bind_param('ssssssssssssssss',
            $source_account_new->instagram_id,
            $source_account_new->username,
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
            $date
        );
        $stmt->execute();
        $stmt->close();

        /* Retrieve the just created / updated row */
        $source_account = Database::get('*', 'instagram_users', ['username' => $user]);

        /* Insert the media posts or update them */
        if(!$source_account->is_private) {
            $media_counter = 1;

            if ($media_response && !empty($media_response)) {
                foreach ($media_response['medias'] as $media) {

                    $hashtags = InstagramHelper::get_hashtags($media->getCaption());
                    $mentions = InstagramHelper::get_mentions($media->getCaption());

                    /* Getting the data for the insertion in the database */
                    $media_data = new StdClass();
                    $media_data->media_id = $media->getId();
                    $media_data->shortcode = $media->getShortCode();
                    $media_data->created_date = $media->getCreatedTime();
                    $media_data->caption = $media->getCaption();
                    $media_data->comments = $media->getCommentsCount();
                    $media_data->likes = $media->getLikesCount();
                    $media_data->media_url = $media->getLink();
                    $media_data->media_images = $media->getSquareImages();
                    $media_data->media_image_url = reset($media_data->media_images);
                    $media_data->type = strtoupper($media->getType());
                    $media_data->mentions = json_encode($mentions);
                    $media_data->hashtags = json_encode($hashtags);


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
                    $stmt->close();

                    /* End if needed */
                    $media_counter++;
                    if ($media_counter >= $settings->instagram_calculator_media_count) break;
                }
            }
        }

        /* Update or insert the check log */
        $log = $database->query("SELECT `id` FROM `instagram_logs` WHERE `instagram_user_id` = '{$source_account->id}' AND DATEDIFF('{$date}', `date`) = 0")->fetch_object();

        if($log) {
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
        } else {
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
}

/* Retrieve last X entries */
$logs = [];

if($date_start && $date_end) {
    $date_start_query = (new DateTime($date_start))->format('Y-m-d H:i:s');
    $date_end_query = (new DateTime($date_end))->modify('+1 day')->format('Y-m-d H:i:s');

    $logs_result = $database->query("SELECT * FROM `instagram_logs` WHERE `instagram_user_id` = '{$source_account->id}' AND (`date` BETWEEN '{$date_start_query}' AND '{$date_end_query}')  ORDER BY `date` DESC");
} else {
    $logs_result = $database->query("SELECT * FROM `instagram_logs` WHERE `instagram_user_id` = '{$source_account->id}' ORDER BY `date` DESC LIMIT 15");
}

while($log = $logs_result->fetch_assoc()) { $logs[] = $log; }
$logs = array_reverse($logs);

/* Generate data for the charts and retrieving the average followers /uploads per day */
$logs_chart = [
    'labels'                    => [],
    'followers'                 => [],
    'following'                 => [],
    'average_engagement_rate'   => []
];

$total_new = [
    'followers' => [],
    'uploads'   => []
];

for($i = 0; $i < count($logs); $i++) {
    $logs_chart['labels'][] = (new \DateTime($logs[$i]['date']))->format($language->global->date->datetime_format);
    $logs_chart['followers'][] = $logs[$i]['followers'];
    $logs_chart['following'][] = $logs[$i]['following'];
    $logs_chart['average_engagement_rate'][] = $logs[$i]['average_engagement_rate'];

    if($i != 0) {
        $total_new['followers'][] = $logs[$i]['followers'] - $logs[$i - 1]['followers'];
        $total_new['uploads'][] = $logs[$i]['uploads'] - $logs[$i - 1]['uploads'];
    }
}

/* reverse it back */
$logs = array_reverse($logs);

/* Defining the chart data */
$logs_chart = generate_chart_data($logs_chart);

/* Defining the future projections data */
$total_days = count($logs) > 1 ? (new \DateTime($logs[count($logs)-1]['date']))->diff((new \DateTime($logs[1]['date'])))->format('%a') : 0;

$average = [
    'followers' => $total_days > 0 ? (int) ceil(array_sum($total_new['followers']) / $total_days) : 0,
    'uploads'   => $total_days > 0 ? (int) ceil((array_sum($total_new['uploads']) / $total_days)) : 0
];

$source_account->details = json_decode($source_account->details);

/* Get details of the medias of the account if existing */
if(!$source_account->is_private) {
    $instagram_media_result = $database->query("SELECT * FROM `instagram_media` WHERE `instagram_user_id` = '{$source_account->id}' ORDER BY `created_date` DESC LIMIT {$settings->instagram_calculator_media_count}");

    /* Start to build data and generate the chart */
    $media_results = [];
    $media_chart = [
        'labels'    => [],
        'likes'     => [],
        'comments'  => [],
        'captions'  => []
    ];

    /* Iterating and storing proper data for charts and later use */
    while($media = $instagram_media_result->fetch_object()) { $media_results[] = $media; }

    $media_results = array_reverse($media_results);

    for($i = 0; $i < count($media_results); $i++) {
        $media_chart['labels'][] = (new \DateTime())->setTimestamp($media_results[$i]->created_date)->format($language->global->date->datetime_format);
        $media_chart['likes'][] = $media_results[$i]->likes;
        $media_chart['comments'][] = $media_results[$i]->comments;
        $media_chart['captions'][] = str_word_count($media_results[$i]->caption);
    }

    $media_results = array_reverse($media_results);

    /* Defining the chart data for media */
    $media_chart = generate_chart_data($media_chart);

}

/* Custom title */
add_event('title', function() {
    global $page_title;
    global $user;
    global $language;

    $page_title = sprintf($language->instagram->report->title, $user);
});

