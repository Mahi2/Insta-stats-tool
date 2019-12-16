<?php
defined('MAHIDCODE') || die();

/* Get the custom page url parameter */
$custom_page_url = (isset($parameters[0])) ? Database::clean_string($parameters[0]) : false;

/* If the custom page url is set then try to get data from the database */
$custom_page = ($custom_page_url) ? Database::get('*', 'pages', ['url' => $custom_page_url]) : false;
