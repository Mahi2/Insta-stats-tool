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

/* Facebook Login / Register */
if($settings->facebook_login) {

    $facebook = new Facebook\Facebook([
        'app_id' => $settings->facebook_app_id,
        'app_secret' => $settings->facebook_app_secret,
        'default_graph_version' => 'v2.2',
    ]);

    $facebook_helper = $facebook->getRedirectLoginHelper();
    $facebook_login_url = $facebook->getRedirectLoginHelper()->getLoginUrl($settings->url . 'login/facebook', ['email', 'public_profile']);
}