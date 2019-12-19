<?php
defined('ALTUMCODE') || die();
User::check_permission(1);

$method     = (isset($parameters[0]) && in_array($parameters[0], ['remove-logo', 'remove-favicon', 'test-email'])) ? $parameters[0] : false;
$url_token 	= (isset($parameters[1])) ? $parameters[1] : false;


if($method && $method == 'remove-logo' && $url_token && Security::csrf_check_session_token('url_token', $url_token)) {
    /* Delete the current log */
    if(!empty($settings->logo) && file_exists(ROOT . UPLOADS_ROUTE . 'logo/' . $settings->logo)) {
        unlink(ROOT . UPLOADS_ROUTE . 'logo/' . $settings->logo);
    }

    /* Remove it from db */
    $database->query("UPDATE `settings` SET `logo` = '' WHERE `id` = 1");

    /* Set message & Redirect */
    $_SESSION['success'][] = $language->global->success_message->basic;
    redirect('admin/website-settings');
}

if($method && $method == 'remove-favicon' && $url_token && Security::csrf_check_session_token('url_token', $url_token)) {

    /* Delete the current log */
    if(!empty($settings->favicon) && file_exists(ROOT . UPLOADS_ROUTE . 'favicon/' . $settings->favicon)) {
        unlink(ROOT . UPLOADS_ROUTE . 'favicon/' . $settings->favicon);
    }

    /* Remove it from db */
    $database->query("UPDATE `settings` SET `favicon` = '' WHERE `id` = 1");

    /* Set message & Redirect */
    $_SESSION['success'][] = $language->global->success_message->basic;
    redirect('admin/website-settings');
}

/* Check if we need to send a test email */
if($method && $method == 'test-email' && $url_token && Security::csrf_check_session_token('url_token', $url_token)) {

    $result = sendmail($settings->smtp_from, $settings->title . ' - Test Email', 'This is just a test email to confirm the smtp email settings!', true);

    if($result->ErrorInfo == '') {
        $_SESSION['success'][] = $language->admin_website_settings->success_message->email;
    } else {
        $_SESSION['error'][] = sprintf($language->admin_website_settings->error_message->email, $result->ErrorInfo);
    }

    redirect('admin/website-settings');
}

if(!empty($_POST)) {