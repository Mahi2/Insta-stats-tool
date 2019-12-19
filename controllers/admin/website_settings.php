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
    /* Define some variables */
    $image_allowed_extensions = ['jpg', 'jpeg', 'png', 'svg', 'ico'];
    $logo = (!empty($_FILES['logo']['name']));
    $logo_name = $logo ? '' : $settings->logo;
    $favicon = (!empty($_FILES['favicon']['name']));
    $favicon_name = $favicon ? '' : $settings->favicon;

    $_POST['title'] = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $_POST['meta_description'] = filter_var($_POST['meta_description'], FILTER_SANITIZE_STRING);
    $_POST['time_zone'] = filter_var($_POST['time_zone'], FILTER_SANITIZE_STRING);
    $_POST['email_confirmation'] = (int)($_POST['email_confirmation']);

    $_POST['store_paypal_client_id'] = filter_var($_POST['store_paypal_client_id'], FILTER_SANITIZE_STRING);
    $_POST['store_paypal_secret'] = filter_var($_POST['store_paypal_secret'], FILTER_SANITIZE_STRING);
    $_POST['store_currency'] = filter_var($_POST['store_currency'], FILTER_SANITIZE_STRING);
    $_POST['store_user_default_points'] = (int) $_POST['store_user_default_points'];

    $_POST['public_key'] = filter_var($_POST['public_key'], FILTER_SANITIZE_STRING);
    $_POST['private_key'] = filter_var($_POST['private_key'], FILTER_SANITIZE_STRING);
    $_POST['facebook_app_id'] = filter_var($_POST['facebook_app_id'], FILTER_SANITIZE_STRING);
    $_POST['facebook_app_secret'] = filter_var($_POST['facebook_app_secret'], FILTER_SANITIZE_STRING);
    $_POST['instagram_client_id'] = filter_var($_POST['instagram_client_id'], FILTER_SANITIZE_STRING);
    $_POST['instagram_client_secret'] = filter_var($_POST['instagram_client_secret'], FILTER_SANITIZE_STRING);
    $_POST['analytics_code'] = filter_var($_POST['analytics_code'], FILTER_SANITIZE_STRING);

    $_POST['facebook'] = filter_var($_POST['facebook'], FILTER_SANITIZE_STRING);
    $_POST['twitter'] = filter_var($_POST['twitter'], FILTER_SANITIZE_STRING);
    $_POST['youtube'] = filter_var($_POST['youtube'], FILTER_SANITIZE_STRING);
    $_POST['instagram'] = filter_var($_POST['instagram'], FILTER_SANITIZE_STRING);

    $_POST['smtp_from'] = filter_var($_POST['smtp_from'], FILTER_SANITIZE_STRING);
    $_POST['smtp_host'] = filter_var($_POST['smtp_host'], FILTER_SANITIZE_STRING);
    $_POST['smtp_port'] = (int) $_POST['smtp_port'];
    $_POST['smtp_encryption'] = filter_var($_POST['smtp_encryption'], FILTER_SANITIZE_STRING);
    $_POST['smtp_user'] = filter_var($_POST['smtp_user'] ?? '', FILTER_SANITIZE_STRING);
    $_POST['smtp_pass'] = $_POST['smtp_pass'] ?? '';
    $_POST['smtp_auth'] = (isset($_POST['smtp_auth'])) ? '1' : '0';

    $_POST['cron_queries'] = (int) $_POST['cron_queries'];
    $_POST['cron_auto_add_missing_logs'] = (int) $_POST['cron_auto_add_missing_logs'];

    $_POST['instagram_calculator_media_count'] = $_POST['instagram_calculator_media_count'] > 30 ? 30 : (int) $_POST['instagram_calculator_media_count'];

    $_POST['proxy'] = (int) $_POST['proxy'];
    $_POST['proxy_exclusive'] = (int) $_POST['proxy_exclusive'];
    $_POST['proxy_timeout'] = (int) $_POST['proxy_timeout'];
    $_POST['proxy_failed_requests_pause'] = (int) $_POST['proxy_failed_requests_pause'];
    $_POST['proxy_pause_duration'] = (int) $_POST['proxy_pause_duration'];

    /* Email templates */
    $activation_email_template = json_encode([
        'subject' => filter_var($_POST['activation_email_subject'], FILTER_SANITIZE_STRING),
        'body' => $_POST['activation_email_body']
    ]);

    $lost_password_email_template = json_encode([
        'subject' => filter_var($_POST['lost_password_email_subject'], FILTER_SANITIZE_STRING),
        'body' => $_POST['lost_password_email_body']
    ]);

    $credentials_email_template = json_encode([
        'subject' => filter_var($_POST['credentials_email_subject'], FILTER_SANITIZE_STRING),
        'body' => $_POST['credentials_email_body']
    ]);

    /* Email notifications */
    $_POST['admin_email_notification_emails'] = str_replace(' ', '', $_POST['admin_email_notification_emails']);
    $_POST['admin_new_user_email_notification'] = (isset($_POST['admin_new_user_email_notification'])) ? '1' : '0';
    $_POST['admin_new_payment_email_notification'] = (isset($_POST['admin_new_payment_email_notification'])) ? '1' : '0';

    /* Check for any errors on the logo image */
    if($logo) {
        $logo_file_name = $_FILES['logo']['name'];
        $logo_file_extension = explode('.', $logo_file_name);
        $logo_file_extension = strtolower(end($logo_file_extension));
        $logo_file_temp = $_FILES['logo']['tmp_name'];
        $logo_file_size = $_FILES['logo']['size'];
        list($logo_width, $logo_height) = getimagesize($logo_file_temp);

        if(!in_array($logo_file_extension, $image_allowed_extensions)) {
            $_SESSION['error'][] = $language->global->error_message->invalid_file_type;
        }

        if(!is_writable(ROOT . UPLOADS_ROUTE . 'logo/')) {
            $_SESSION['error'][] = sprintf($language->global->error_message->directory_not_writeable, ROOT . UPLOADS_ROUTE . 'logo/');
        }

        if(empty($_SESSION['error'])) {