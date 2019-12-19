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