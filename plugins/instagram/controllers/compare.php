<?php
defined('ALTUMCODE') || die();

$custom_title = sprintf($language->compare->title_dynamic, $user_one, $user_two);

$source_account_one_details = json_decode($source_account_one->details);
$source_account_two_details = json_decode($source_account_two->details);

/* Generate the chart logs */
$logs = [];
$logs_result_one = $database->query("SELECT * FROM `instagram_logs` WHERE `instagram_user_id` = '{$source_account_one->id}' ORDER BY `date` DESC LIMIT 15");
$logs_result_two = $database->query("SELECT * FROM `instagram_logs` WHERE `instagram_user_id` = '{$source_account_two->id}' ORDER BY `date` DESC LIMIT 15");

while($log = $logs_result_one->fetch_assoc()) {

    $date = (new DateTime($log['date']))->format($language->global->date->datetime_format);

    $logs[$date][$log['username']] = [
        'followers'                 => $log['followers'],
        'average_engagement_rate'   => $log['average_engagement_rate']
    ];
}

while($log = $logs_result_two->fetch_assoc()) {

    $date = (new DateTime($log['date']))->format($language->global->date->datetime_format);

    $logs[$date][$log['username']] = [
        'followers'                 => $log['followers'],
        'average_engagement_rate'   => $log['average_engagement_rate']
    ];

}

/* Sort the logs by date */
ksort($logs);

/* Generate data for the charts and retrieving the average followers /uploads per day */
$chart_labels_array = [];
$chart_followers_one_array = $chart_followers_two_array = $chart_average_engagement_rate_one_array = $chart_average_engagement_rate_two_array = [];

if($language->direction == 'rtl') {
    $logs = array_reverse($logs);
}

foreach($logs as $key => $log) {
    $chart_labels_array[] = $key;

    $chart_followers_one_array[] = array_key_exists($user_one, $log) ? $log[$user_one]['followers'] : false;
    $chart_followers_two_array[] = array_key_exists($user_two, $log) ? $log[$user_two]['followers'] : false;

    $chart_average_engagement_rate_one_array[] = array_key_exists($user_one, $log) ? $log[$user_one]['average_engagement_rate'] : false;
    $chart_average_engagement_rate_two_array[] = array_key_exists($user_two, $log) ? $log[$user_two]['average_engagement_rate'] : false;

}


/* Defining the chart data */
$chart_labels = '["' . implode('", "', $chart_labels_array) . '"]';
$chart_followers_one = '[' . implode(', ', $chart_followers_one_array) . ']';
$chart_followers_two = '[' . implode(', ', $chart_followers_two_array) . ']';
$chart_average_engagement_rate_one = '[' . implode(', ', $chart_average_engagement_rate_one_array) . ']';
$chart_average_engagement_rate_two = '[' . implode(', ', $chart_average_engagement_rate_two_array) . ']';
