<?php
defined('MAHIDCODE') || die();
User::logged_in_redirect();

$redirect = 'dashboard';
if(isset($_GET['redirect']) && $redirect = $_GET['redirect']) {
    //
}

/* Default variables */
$register_username = $register_name = $register_email = '';

/* instagram login / register handler */
if($settings->instagram_login) {

    $instagram = new MetzWeb\Instagram\Instagram([
        'apiKey' => $settings->instagram_client_id,
        'apiSecret' => $settings->instagram_client_secret,
        'apiCallback' => $settings->url . 'login/instagram'
    ]);

    $instagram_login_url = $instagram->getLoginUrl();
}