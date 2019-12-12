<?php
defined('MAHIDCODE') || die();

require 'init.php';

/* Controller */
require CONTROLLERS_ROUTE . $route . $controller . '.php';

/* Establish the title of the page */
require_once 'includes/titles.php';

/* View */
if($controller_has_view) {
    require VIEWS_ROUTE . $route . 'wrapper.php';
}

require 'deinit.php';
