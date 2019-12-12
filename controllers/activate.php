<?php
defined('MAHIDCODE') || die();

$md5_email = (isset($parameters[0])) ? $parameters[0] : false;
$email_activation_code = (isset($parameters[1])) ? $parameters[1] : false;

if(!$md5_email || !$email_activation_code) redirect();