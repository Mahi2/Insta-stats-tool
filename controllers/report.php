<?php
defined('MAHIDCODE') || die();

$user = isset($parameters[0]) ? Database::clean_string($parameters[0]) : false;
$source = isset($parameters[1]) && in_array($parameters[1], $sources) ? Database::clean_string($parameters[1]) : reset($sources);
$date_start = isset($parameters[2]) ? Database::clean_string($parameters[2]) : false;
$date_end = isset($parameters[3]) ? Database::clean_string($parameters[3]) : false;
$date_string = ($date_start && $date_end && validate_date($date_start, 'Y-m-d') && validate_date($date_end, 'Y-m-d')) ? $date_start . ',' . $date_end : false;

$refresh = isset($_GET['refresh']) && Security::csrf_check_session_token('url_token', $_GET['refresh']);

if(!$user || ($source !== 'instagram' && !$plugins->exists_and_active($source))) redirect();

$is_proxy_request = false;