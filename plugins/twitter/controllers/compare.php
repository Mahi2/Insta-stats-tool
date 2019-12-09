<?php
defined('ALTUMCODE') || die();

$custom_title = sprintf($language->compare->title_dynamic, $user_one, $user_two);


/* Generate the chart logs */
$logs = [];
$logs_result_one = $database->query("SELECT * FROM `twitter_logs` WHERE `twitter_user_id` = '{$source_account_one->id}' ORDER BY `date` DESC LIMIT 15");
$logs_result_two = $database->query("SELECT * FROM `twitter_logs` WHERE `twitter_user_id` = '{$source_account_two->id}' ORDER BY `date` DESC LIMIT 15");

while($log = $logs_result_one->fetch_assoc()) {

    $date = (new DateTime($log['date']))->format($language->global->date->datetime_format);

    $logs[$date][$user_one] = [
        'followers'  => $log['followers'],
        'tweets'        => $log['tweets'],
    ];
}

while($log = $logs_result_two->fetch_assoc()) {

    $date = (new DateTime($log['date']))->format($language->global->date->datetime_format);

    $logs[$date][$user_two] = [
        'followers'  => $log['followers'],
        'tweets'        => $log['tweets'],
    ];
}

/* Sort the logs by date */
ksort($logs);

/* Generate data for the charts and retrieving the average followers /uploads per day */
$chart_labels_array = [];
$chart_followers_one_array = $chart_followers_two_array = $chart_tweets_one_array = $chart_tweets_two_array = [];

if($language->direction == 'rtl') {
    $logs = array_reverse($logs);
}

foreach($logs as $key => $log) {
    $chart_labels_array[] = $key;

    $chart_followers_one_array[] = array_key_exists($user_one, $log) ? $log[$user_one]['followers'] : false;
    $chart_followers_two_array[] = array_key_exists($user_two, $log) ? $log[$user_two]['followers'] : false;

    $chart_tweets_one_array[] = array_key_exists($user_one, $log) ? $log[$user_one]['tweets'] : false;
    $chart_tweets_two_array[] = array_key_exists($user_two, $log) ? $log[$user_two]['tweets'] : false;
}


/* Defining the chart data */
$chart_labels = '["' . implode('", "', $chart_labels_array) . '"]';
$chart_followers_one = '[' . implode(', ', $chart_followers_one_array) . ']';
$chart_followers_two = '[' . implode(', ', $chart_followers_two_array) . ']';
$chart_tweets_one = '[' . implode(', ', $chart_tweets_one_array) . ']';
$chart_tweets_two = '[' . implode(', ', $chart_tweets_two_array) . ']';
