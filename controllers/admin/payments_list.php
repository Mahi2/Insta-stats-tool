<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

/* Insert the needed libraries */
add_event('head', function() {
    global $settings;

    echo '<link href="' . $settings->url . ASSETS_ROUTE . 'css/datatables.min.css" rel="stylesheet" media="screen">';
    echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/datatables.min.js"></script>';

});