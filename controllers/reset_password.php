<?php
defined('MAHIDCODE') || die();
User::logged_in_redirect();

$email = (isset($parameters[0])) ? $parameters[0] : false;
$lost_password_code = (isset($parameters[1])) ? $parameters[1] : false;

if(!$email || !$lost_password_code) redirect();