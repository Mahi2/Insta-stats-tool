<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$source = isset($parameters[0]) && in_array($parameters[0], $sources) ? Database::clean_string($parameters[0]) : reset($sources);
$type = isset($parameters[1]) ? Database::clean_string($parameters[1]) : false;

if($type && $type == 'ajax') {
    require_once $plugins->require($source, 'controllers/admin/users_management_ajax');
} else {
    require_once $plugins->require($source, 'controllers/admin/users_management');

    /* Insert the needed libraries */
    add_event('head', function() {
        global $settings;

        echo '<link href="' . $settings->url . ASSETS_ROUTE . 'css/datatables.min.css" rel="stylesheet" media="screen">';
        echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/datatables.min.js"></script>';

    });

    /* Custom title */
    add_event('title', function() {
        global $page_title;
        global $language;
        global $source;

        $page_title = $language->{$source}->admin_users_management->title;
    });

}
