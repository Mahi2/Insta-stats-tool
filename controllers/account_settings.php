<?php
defined('ALTUMCODE') || die();
User::check_permission(0);

$method 	= (isset($parameters[0])) ? $parameters[0] : false;
$url_token 	= (isset($parameters[1])) ? $parameters[1] : false;