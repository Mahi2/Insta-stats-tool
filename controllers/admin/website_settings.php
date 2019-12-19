<?php
defined('ALTUMCODE') || die();
User::check_permission(1);

$method     = (isset($parameters[0]) && in_array($parameters[0], ['remove-logo', 'remove-favicon', 'test-email'])) ? $parameters[0] : false;
$url_token 	= (isset($parameters[1])) ? $parameters[1] : false;


if($method && $method == 'remove-logo' && $url_token && Security::csrf_check_session_token('url_token', $url_token)) {
