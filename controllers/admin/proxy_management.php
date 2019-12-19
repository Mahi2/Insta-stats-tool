<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$type 		= (isset($parameters[0])) ? $parameters[0] : false;
$proxy_id 	= (isset($parameters[1])) ? (int) $parameters[1] : false;
$url_token	= (isset($parameters[2])) ? $parameters[2] : false;

$default_values = [
    'address' => '',
    'port' => '',
    'username' => '',
    'password' => '',
    'note' => ''
];

if(isset($type) && $type == 'delete') {

    /* Check for errors and permissions */
	if(!Security::csrf_check_session_token('url_token', $url_token)) {
		$_SESSION['error'][] = $language->global->error_message->invalid_token;
	}

	if(empty($_SESSION['error'])) {
		$database->query("DELETE FROM `proxies` WHERE `proxy_id` = {$proxy_id}");

		$_SESSION['success'][] = $language->global->success_message->basic;
	}

    redirect('admin/proxies-management');
}

if(isset($type) && $type == 'test') {

    $proxy = Database::get('*', 'proxies', ['proxy_id' => $proxy_id]);