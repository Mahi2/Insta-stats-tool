<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$type 		= isset($parameters[0]) ? $parameters[0] : false;
$id 	    = isset($parameters[1]) ? Database::clean_string($parameters[1]) : false;
$url_token	= isset($parameters[2]) ? $parameters[2] : false;
$source     = isset($parameters[3]) && in_array($parameters[3], $sources) ? $parameters[3] : reset($sources);

if(isset($type) && $type == 'plugin_status') {
    /* Check for errors and permissions */
    if(!Security::csrf_check_session_token('url_token', $url_token)) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }

    if(!$plugin = Database::get(['status'], 'plugins', ['identifier' => $id])) {
        redirect('admin/extra-settings');
    }

    if(empty($_SESSION['error'])) {
        $new_status = (int) !$plugin->status;
        $database->query("UPDATE `plugins` SET `status` = {$new_status} WHERE `identifier` = '{$id}'");

        $_SESSION['success'][] = $language->global->success_message->basic;

        redirect('admin/extra-settings');
    }
}

if(isset($type) && $type == 'demo_delete') {