<?php
defined('MAHIDCODE') || die();
User::logged_in_redirect();

$redirect = 'dashboard';
if(isset($_GET['redirect']) && $redirect = $_GET['redirect']) {
    //
}