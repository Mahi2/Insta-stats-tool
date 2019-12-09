<?php
defined('ALTUMCODE') || die();

$custom_title = sprintf($language->compare->title_dynamic, $user_one, $user_two);


/* Generate the chart logs */
$logs = [];
$logs_result_one = $database->query("SELECT * FROM `facebook_logs` WHERE `facebook_user_id` = '{$source_account_one->id}' ORDER BY `date` DESC LIMIT 15");
$logs_result_two = $database->query("SELECT * FROM `facebook_logs` WHERE `facebook_user_id` = '{$source_account_two->id}' ORDER BY `date` DESC LIMIT 15");

while($log = $logs_result_one->fetch_assoc()) {

    $date = (new DateTime($log['date']))->format($language->global->date->datetime_format);

    $logs[$date][$log['username']] = [
        'likes'                     => $log['likes'],
        'followers'        => $log['followers']
    ];
}

while($log = $logs_result_two->fetch_assoc()) {

    $date = (new DateTime($log['date']))->format($language->global->date->datetime_format);

    $logs[$date][$log['username']] = [
        'likes'                     => $log['likes'],
        'followers'        => $log['followers']
    ];
}

/* Sort the logs by date */
ksort($logs);

/* Generate data for the charts and retrieving the average likes /uploads per day */
$chart_labels_array = [];
$chart_likes_one_array = $chart_likes_two_array = $chart_followers_one_array = $chart_followers_two_array = [];

if($language->direction == 'rtl') {
    $logs = array_reverse($logs);
}

foreach($logs as $key => $log) {
    $chart_labels_array[] = $key;

    $chart_likes_one_array[] = array_key_exists($user_one, $log) ? $log[$user_one]['likes'] : false;
    $chart_likes_two_array[] = array_key_exists($user_two, $log) ? $log[$user_two]['likes'] : false;

    $chart_followers_one_array[] = array_key_exists($user_one, $log) ? $log[$user_one]['followers'] : false;
    $chart_followers_two_array[] = array_key_exists($user_two, $log) ? $log[$user_two]['followers'] : false;

}


/* Defining the chart data */
$chart_labels = '["' . implode('", "', $chart_labels_array) . '"]';
$chart_likes_one = '[' . implode(', ', $chart_likes_one_array) . ']';
$chart_likes_two = '[' . implode(', ', $chart_likes_two_array) . ']';
$chart_followers_one = '[' . implode(', ', $chart_followers_one_array) . ']';
$chart_followers_two = '[' . implode(', ', $chart_followers_two_array) . ']';
