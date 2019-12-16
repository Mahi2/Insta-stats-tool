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

/* Initiate captcha */
$captcha = new Captcha($settings->recaptcha, $settings->public_key, $settings->private_key);

if(!empty($_POST)) {
    /* Clean some posted variables */
    $_POST['username']	= generate_slug(filter_var($_POST['username'], FILTER_SANITIZE_STRING));
    $_POST['name']		= filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $_POST['email']		= filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    /* Default variables */
    $register_username = $_POST['username'];
    $register_name = $_POST['name'];
    $register_email = $_POST['email'];

    /* Define some variables */
    $fields = ['username', 'name', 'email' ,'password'];

    /* Check for any errors */
    foreach($_POST as $key=>$value) {
        if(empty($value) && in_array($key, $fields) == true) {
            $_SESSION['error'][] = $language->global->error_message->empty_fields;
            break 1;
        }
    }
    if(!$captcha->is_valid()) {
        $_SESSION['error'][] = $language->global->error_message->invalid_captcha;
    }
    if(strlen($_POST['username']) < 3 || strlen($_POST['username']) > 32) {
        $_SESSION['error'][] = $language->register->error_message->username_length;
    }
    if(strlen($_POST['name']) < 3 || strlen($_POST['name']) > 32) {
        $_SESSION['error'][] = $language->register->error_message->name_length;
    }
    if(Database::exists('user_id', 'users', ['username' => $_POST['username']])) {
        $_SESSION['error'][] = sprintf($language->register->error_message->user_exists, $_POST['username']);
    }
    if(Database::exists('user_id', 'users', ['email' => $_POST['email']])) {
        $_SESSION['error'][] = $language->register->error_message->email_exists;
    }
    if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
        $_SESSION['error'][] = $language->register->error_message->invalid_email;
    }
    if(strlen(trim($_POST['password'])) < 6) {
        $_SESSION['error'][] = $language->register->error_message->short_password;
    }
    $regex = '/^[A-Za-z0-9]+[A-Za-z0-9_.]*[A-Za-z0-9]+$/';
    if(!preg_match($regex, $_POST['username'])) {
        $_SESSION['error'][] = $language->register->error_message->username_characters;
    }