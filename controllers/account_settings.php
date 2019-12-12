<?php
defined('ALTUMCODE') || die();
User::check_permission(0);

$method 	= (isset($parameters[0])) ? $parameters[0] : false;
$url_token 	= (isset($parameters[1])) ? $parameters[1] : false;

if(!empty($_POST)) {

    /* Clean some posted variables */
    $_POST['email']		    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $_POST['username']		= generate_slug(filter_var($_POST['username'], FILTER_SANITIZE_STRING));
    $_POST['name']		= filter_var($_POST['name'], FILTER_SANITIZE_STRING);
