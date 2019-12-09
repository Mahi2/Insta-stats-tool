<?php
defined('ALTUMCODE') || die();

$custom_title = sprintf($language->compare->title_dynamic, $user_one, $user_two);


/* Generate the chart logs */
$logs = [];
$logs_result_one = $database->query("SELECT * FROM `youtube_logs` WHERE `youtube_user_id` = '{$source_account_one->id}' ORDER BY `date` DESC LIMIT 15");
$logs_result_two = $database->query("SELECT * FROM `youtube_logs` WHERE `youtube_user_id` = '{$source_account_two->id}' ORDER BY `date` DESC LIMIT 15");

while($log = $logs_result_one->fetch_assoc()) {

    $date = (new DateTime($log['date']))->format($language->global->date->datetime_format);

    $logs[$date][$user_one] = [
        'subscribers'  => $log['subscribers'],
        'views'        => $log['views'],
        'videos'       => $log['videos']
    ];
}

while($log = $logs_result_two->fetch_assoc()) {

    $date = (new DateTime($log['date']))->format($language->global->date->datetime_format);

    $logs[$date][$user_two] = [
        'subscribers'  => $log['subscribers'],
        'views'        => $log['views'],
        'videos'       => $log['videos']
    ];
}

/* Sort the logs by date */
ksort($logs);

/* Generate data for the charts and retrieving the average subscribers /uploads per day */
$chart_labels_array = [];
$chart_subscribers_one_array = $chart_subscribers_two_array = $chart_views_one_array = $chart_views_two_array = $chart_videos_one_array = $chart_videos_two_array = [];

if($language->direction == 'rtl') {
    $logs = array_reverse($logs);
}

foreach($logs as $key => $log) {
    $chart_labels_array[] = $key;

    $chart_subscribers_one_array[] = array_key_exists($user_one, $log) ? $log[$user_one]['subscribers'] : false;
    $chart_subscribers_two_array[] = array_key_exists($user_two, $log) ? $log[$user_two]['subscribers'] : false;

    $chart_views_one_array[] = array_key_exists($user_one, $log) ? $log[$user_one]['views'] : false;
    $chart_views_two_array[] = array_key_exists($user_two, $log) ? $log[$user_two]['views'] : false;

    $chart_videos_one_array[] = array_key_exists($user_one, $log) ? $log[$user_one]['videos'] : false;
    $chart_videos_two_array[] = array_key_exists($user_two, $log) ? $log[$user_two]['videos'] : false;
}


/* Defining the chart data */
$chart_labels = '["' . implode('", "', $chart_labels_array) . '"]';
$chart_subscribers_one = '[' . implode(', ', $chart_subscribers_one_array) . ']';
$chart_subscribers_two = '[' . implode(', ', $chart_subscribers_two_array) . ']';
$chart_views_one = '[' . implode(', ', $chart_views_one_array) . ']';
$chart_views_two = '[' . implode(', ', $chart_views_two_array) . ']';
$chart_videos_one = '[' . implode(', ', $chart_videos_one_array) . ']';
$chart_videos_two = '[' . implode(', ', $chart_videos_two_array) . ']';
