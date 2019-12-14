<?php
defined('MAHIDCODE') || die();
User::check_permission(0);

$source = isset($parameters[0]) && in_array($parameters[0], $sources) ? Database::clean_string($parameters[0]) : reset($sources);