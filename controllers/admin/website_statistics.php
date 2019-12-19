<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$date_start = isset($parameters[0]) ? Database::clean_string($parameters[0]) : (new DateTime())->modify('-30 day')->format('Y-m-d');
$date_end = isset($parameters[1]) ? Database::clean_string($parameters[1]) : (new DateTime())->format('Y-m-d');
$date_string = ($date_start && $date_end && validate_date($date_start, 'Y-m-d') && validate_date($date_end, 'Y-m-d')) ? $date_start . ',' . $date_end : false;


/* Insert the chartjs library */
add_event('head', function() {
    global $settings;

    echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/Chart.bundle.min.js"></script>';
    echo '<link href="' . $settings->url . ASSETS_ROUTE . 'css/datepicker.min.css" rel="stylesheet" media="screen">';
    echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/datepicker.min.js"></script>';
    echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/i18n/datepicker.en.js"></script>';

});
