<?php

/* Add the prefix if needed */
$language_string = $route_key == '' ? $controller : $route_key . '_' . $controller;

/* Check if the default is viable and use it */
$page_title = (isset($language->$language_string->title)) ? $language->$language_string->title : $controller;

/* Custom titles */
perform_event('title');

/* Append the title of the site */
$page_title .= ' - ' . $settings->title;

