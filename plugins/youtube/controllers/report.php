<?php
defined('ALTUMCODE') || die();
/* For YouTube source there is no proxy handling because it uses a direct API */

/* We need to check if the user already exists in our database */
$source_account = $database->query("SELECT * FROM `youtube_users` WHERE `youtube_id` = '{$user}' OR `username` = '{$user}' LIMIT 1")->fetch_object() ?? false;

if(!$source_account || ($source_account && (new \DateTime())->modify('-'.$settings->youtube_check_interval.' hours') > (new \DateTime($source_account->last_check_date)))) {

    $youtube = new YouTube();

    /* Set the needed parameters */
    $youtube->set_header_referer($settings->url);
    $youtube->set_type('channels');
    $youtube->set_parameters(['part' => 'snippet,statistics,contentDetails']);
    $youtube->set_parameters(['key' => $settings->youtube_api_key]);

    /* Search with the username if the account is not already added in the database */
    $source_account_data = false;

    /* Try to find the youtube account by username or id */
    if(!$source_account) {
        $youtube->set_parameters(['forUsername' => $user]);

        try {
            $source_account_data = $youtube->get();
        } catch (Exception $error) {
            $source_account_data = false;
        }

        /* Prepare a new request if we dont have results */
        if(!$source_account_data) {
            $youtube->set_parameters(['id' => $user]);
            $youtube->set_parameters(['forUsername' => false]);
        }
    }

    /* We already have it in the database so we prepare it */
    else {

        $youtube->set_parameters(['id' => $source_account->youtube_id]);
        $youtube->set_parameters(['forUsername' => false]);

    }


    if(!$source_account_data) {
        try {
            $source_account_data = $youtube->get();
        } catch (Exception $error) {
            $error_message = $_SESSION['error'][] = $error->getMessage();

            redirect();
        }

        /* Make sure we have results */
        if(!$source_account_data) {
            $error_message = $_SESSION['error'][] = $language->youtube->report->error_message->not_found;

            redirect();
        }


        /* Check if the account needs to be added and has more than needed followers */
        if(!$source_account) {
            if($source_account_data[0]->statistics->subscriberCount < $settings->youtube_minimum_subscribers) {
                $_SESSION['error'][] = sprintf($language->youtube->report->error_message->low_subscribers, $settings->youtube_minimum_subscribers);
            }

            if(!empty($_SESSION['error'])) redirect();

        }
    }


    /* Vars to be added & used */
    $source_account_new = new StdClass();
    $source_account_new->youtube_id = $source_account_data[0]->id;
    $source_account_new->username = $source_account_data[0]->snippet->customUrl ?? null;
    $source_account_new->title = $source_account_data[0]->snippet->title;
    $source_account_new->description = $source_account_data[0]->snippet->description;
    $source_account_new->subscribers = $source_account_data[0]->statistics->subscriberCount;
    $source_account_new->views = $source_account_data[0]->statistics->viewCount;
    $source_account_new->videos = $source_account_data[0]->statistics->videoCount;
    $source_account_new->profile_picture_url = $source_account_data[0]->snippet->thumbnails->high->url ?? '';
    $source_account_new->uploads_playlist_id = $source_account_data[0]->contentDetails->relatedPlaylists->uploads;

    /* Get extra details from the response */
    $details = [
        'country'       => $source_account_data[0]->snippet->country ?? null,
        'created_date'  => isset($source_account_data[0]->snippet->publishedAt) ? (new \DateTime($source_account_data[0]->snippet->publishedAt))->format('Y-m-d H:i:s') : ''
    ];
    $details = json_encode($details);


    /* Send a new request to get the last uploaded items */
    if($settings->youtube_check_videos) {
        $youtube->set_type('playlistItems');
        $youtube->set_parameters(['part' => 'snippet']);
        $youtube->set_parameters(['playlistId' => $source_account_data[0]->contentDetails->relatedPlaylists->uploads]);
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
    }

    /* Insert into db */
    $stmt = $database->prepare("INSERT INTO `youtube_users` (
        `youtube_id`,
        `username`,
        `title`,
        `description`,
        `profile_picture_url`,
        `subscribers`,
        `views`,
        `videos`,
        `uploads_playlist_id`,
        `details`,
        `added_date`,
        `last_check_date`,
        `last_successful_check_date`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        `username` = VALUES (username),
        `title` = VALUES (title),
        `description` = VALUES (description),
        `profile_picture_url` = VALUES (profile_picture_url),
        `subscribers` = VALUES (subscribers),
        `views` = VALUES (views),
        `videos` = VALUES (videos),
        `uploads_playlist_id` = VALUES (uploads_playlist_id),
        `details` = VALUES (details),
        `last_check_date` = VALUES (last_check_date),
        `last_successful_check_date` = VALUES (last_successful_check_date)
    ");
    $stmt->bind_param('sssssssssssss',
        $source_account_new->youtube_id,
        $source_account_new->username,
        $source_account_new->title,
        $source_account_new->description,
        $source_account_new->profile_picture_url,
        $source_account_new->subscribers,
        $source_account_new->views,
        $source_account_new->videos,
        $source_account_new->uploads_playlist_id,
        $details,
        $date,
        $date,
        $date
    );
    $stmt->execute();
    $stmt->close();


    /* Retrieve the just created / updated row */
    $source_account = Database::get('*', 'youtube_users', ['youtube_id' => $source_account_new->youtube_id]);

    /* Update or insert the check log */
    $log = $database->query("SELECT `id` FROM `youtube_logs` WHERE `youtube_user_id` = '{$source_account->id}' AND DATEDIFF('{$date}', `date`) = 0")->fetch_object();

    if($log) {
        Database::update(
            'youtube_logs',
            [
                'subscribers'   => $source_account->subscribers,
                'views'         => $source_account->views,
                'videos'        => $source_account->videos,
                'date'          => $date
            ],
            ['id' => $log->id]
        );
    } else {
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

    /* Update or insert the videos */
    if($settings->youtube_check_videos && $source_account_videos) {

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

/* Retrieve last X entries */
$logs = [];

if($date_start && $date_end) {
    $date_start_query = (new DateTime($date_start))->format('Y-m-d H:i:s');
    $date_end_query = (new DateTime($date_end))->modify('+1 day')->format('Y-m-d H:i:s');

    $logs_result = $database->query("SELECT * FROM `youtube_logs` WHERE `youtube_user_id` = '{$source_account->id}' AND (`date` BETWEEN '{$date_start_query}' AND '{$date_end_query}')  ORDER BY `date` DESC");
} else {
    $logs_result = $database->query("SELECT * FROM `youtube_logs` WHERE `youtube_user_id` = '{$source_account->id}' ORDER BY `date` DESC LIMIT 15");
}


while($log = $logs_result->fetch_assoc()) { $logs[] = $log; }
$logs = array_reverse($logs);

/* Generate data for the charts and retrieving the average followers /uploads per day */
$logs_chart = [
    'labels'        => [],
    'subscribers'   => [],
    'views'         => [],
    'videos'        => []
];

$total_new = [
    'subscribers'   => [],
    'views'         => [],
    'videos'        => []
];

for($i = 0; $i < count($logs); $i++) {
    $logs_chart['labels'][] = (new \DateTime($logs[$i]['date']))->format($language->global->date->datetime_format);
    $logs_chart['subscribers'][] = $logs[$i]['subscribers'];
    $logs_chart['views'][] = $logs[$i]['views'];
    $logs_chart['videos'][] = $logs[$i]['videos'];

    if($i != 0) {
        $total_new['subscribers'][] = $logs[$i]['subscribers'] - $logs[$i - 1]['subscribers'];
        $total_new['views'][] = $logs[$i]['views'] - $logs[$i - 1]['views'];
        $total_new['videos'][] = $logs[$i]['videos'] - $logs[$i - 1]['videos'];
    }
}

/* reverse it back */
$logs = array_reverse($logs);

/* Defining the chart data */
$logs_chart = generate_chart_data($logs_chart);

/* Defining the future projections data */
$total_days = count($logs) > 1 ? (new \DateTime($logs[count($logs)-1]['date']))->diff((new \DateTime($logs[1]['date'])))->format('%a') : 0;

$average = [
    'subscribers'   => $total_days > 0 ? (int) ceil(array_sum($total_new['subscribers']) / $total_days) : 0,
    'views'         => $total_days > 0 ? (int) ceil(array_sum($total_new['views']) / $total_days) : 0,
    'videos'        => $total_days > 0 ? (int) ceil(array_sum($total_new['videos']) / $total_days) : 0,
];

$source_account->details = json_decode($source_account->details);

/* Get the youtube videos if enabled */
if($settings->youtube_check_videos) {
    $source_account_videos_result = $database->query("SELECT * FROM `youtube_videos` WHERE `youtube_user_id` = {$source_account->id} ORDER BY `created_date` DESC LIMIT {$settings->youtube_check_videos}");
    $videos_results = [];

    $videos_chart = [
        'labels'    => [],
        'views'     => [],
        'likes'     => [],
        'dislikes'  => [],
        'comments'  => []
    ];

    /* Iterating and storing proper data for charts and later use */
    while($row = $source_account_videos_result->fetch_object()) { $videos_results[] = $row; }

    $videos_results = array_reverse($videos_results);

    for($i = 0; $i < count($videos_results); $i++) {
        $videos_chart['labels'][] = (new \DateTime($videos_results[$i]->created_date))->format($language->global->date->datetime_format);
        $videos_chart['views'][] = $videos_results[$i]->views;
        $videos_chart['likes'][] = $videos_results[$i]->likes;
        $videos_chart['dislikes'][] = $videos_results[$i]->dislikes;
        $videos_chart['comments'][] = $videos_results[$i]->comments;
    }

    $videos_results = array_reverse($videos_results);

    /* Defining the chart data for media */
    $videos_chart = generate_chart_data($videos_chart);

}

/* Custom title */
add_event('title', function() {
    global $page_title;
    global $source_account;
    global $language;

    $page_title = sprintf($language->youtube->report->title, $source_account->title);
});

