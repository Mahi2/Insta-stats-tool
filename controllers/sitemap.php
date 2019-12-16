<?php
defined('MAHIDCODE') || die();

$limit = (isset($parameters[0])) ? (int) Database::clean_string($parameters[0]) : false;

/* Set the header as xml so the browser can read it properly */
header('Content-Type: text/xml');

/* Default pagination */
$pagination = 9000;