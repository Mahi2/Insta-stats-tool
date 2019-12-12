<?php
defined('MAHIDCODE') || die();

$source = isset($parameters[0]) && in_array($parameters[0], $sources) ? Database::clean_string($parameters[0]) : reset($sources);
$user_one = isset($parameters[1]) ? Database::clean_string($parameters[1]) : false;
$user_two = isset($parameters[2]) ? Database::clean_string($parameters[2]) : false;

$table = $source . '_users';
$column = $source != 'youtube' ? 'username' : 'youtube_id';
