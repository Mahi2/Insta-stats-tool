<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$type 		= isset($parameters[0]) ? $parameters[0] : false;
$id 	    = isset($parameters[1]) ? Database::clean_string($parameters[1]) : false;
$url_token	= isset($parameters[2]) ? $parameters[2] : false;
$source     = isset($parameters[3]) && in_array($parameters[3], $sources) ? $parameters[3] : reset($sources);

if(isset($type) && $type == 'plugin_status') {