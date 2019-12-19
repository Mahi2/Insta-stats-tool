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
            /* Delete current logo */
            if(!empty($settings->logo) && file_exists(ROOT . UPLOADS_ROUTE . 'logo/' . $settings->logo)) {
                unlink(ROOT . UPLOADS_ROUTE . 'logo/' . $settings->logo);
            }

            /* Generate new name for logo */
            $logo_new_name = md5(time() . rand()) . '.' . $logo_file_extension;

            /* Upload the original */
            move_uploaded_file($logo_file_temp, ROOT . UPLOADS_ROUTE . 'logo/' . $logo_new_name);

            /* Execute query */
            $database->query("UPDATE `settings` SET `logo` = '{$logo_new_name}' WHERE `id` = 1");

        }
    }

    /* Check for any errors on the logo image */
    if($favicon) {
        $favicon_file_name = $_FILES['favicon']['name'];
        $favicon_file_extension = explode('.', $favicon_file_name);
        $favicon_file_extension = strtolower(end($favicon_file_extension));
        $favicon_file_temp = $_FILES['favicon']['tmp_name'];
        $favicon_file_size = $_FILES['favicon']['size'];
        list($favicon_width, $favicon_height) = getimagesize($favicon_file_temp);

        if(!in_array($favicon_file_extension, $image_allowed_extensions)) {
            $_SESSION['error'][] = $language->global->error_message->invalid_file_type;
        }

        if(!is_writable(ROOT . UPLOADS_ROUTE . 'favicon/')) {
            $_SESSION['error'][] = sprintf($language->global->error_message->directory_not_writeable, ROOT . UPLOADS_ROUTE . 'favicon/');
        }

        if(empty($_SESSION['error'])) {
            /* Delete current favicon */
            if(!empty($settings->favicon) && file_exists(ROOT . UPLOADS_ROUTE . 'favicon/' . $settings->favicon)) {
                unlink(ROOT . UPLOADS_ROUTE . 'favicon/' . $settings->favicon);
            }

            /* Generate new name for favicon */
            $favicon_new_name = md5(time() . rand()) . '.' . $favicon_file_extension;

            /* Upload the original */
            move_uploaded_file($favicon_file_temp, ROOT . UPLOADS_ROUTE . 'favicon/' . $favicon_new_name);

            /* Execute query */
            $database->query("UPDATE `settings` SET `favicon` = '{$favicon_new_name}' WHERE `id` = 1");

        }
    }

    if(!Security::csrf_check_session_token('form_token', $_POST['form_token'])) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }

    if(empty($_SESSION['error'])) {
        /* Prepare the statement and execute query */
        $stmt = $database->prepare("
            UPDATE
                `settings`
            SET
                `title` = ?,
                `default_language` = ?,
                `meta_description` = ?,
                `meta_keywords` = ?,
                `time_zone` = ?,
                `email_confirmation` = ?,
                `directory` = ?,
                `directory_pagination` = ?,
                
                `store_paypal_client_id` = ?,
                `store_paypal_secret` = ?,
                `store_paypal_mode` = ?,
                `store_stripe_publishable_key` = ?,
                `store_stripe_secret_key` = ?,
                `store_stripe_webhook_secret` = ?,
                `store_currency` = ?,
                `store_unlock_report_price` = TRUNCATE(?, 2),
                `store_unlock_report_time` = ?,
                `store_no_ads_price` = TRUNCATE(?, 2),
                `store_user_default_points` = ?,
    
                `report_ad` = ?,
                `index_ad` = ?,
                `account_sidebar_ad` = ?,
    
                `recaptcha` = ?,
                `public_key` = ?,
                `private_key` = ?,
                `facebook_login` = ?,
                `facebook_app_id` = ?,
                `facebook_app_secret` = ?,
                `instagram_login` = ?,
                `instagram_client_id` = ?,
                `instagram_client_secret` = ?,
                `analytics_code` = ?,
    
                `facebook` = ?,
                `twitter` = ?,
                `youtube` = ?,
                `instagram` = ?,
    
                `smtp_host` = ?,
                `smtp_port` = ?,
                `smtp_encryption` = ?,
                `smtp_auth` = ?,
                `smtp_user` = ?,
                `smtp_pass` = ?,
                `smtp_from` = ?,
                
                `cron_queries` = ?,
                `cron_mode` = ?,
                `cron_auto_add_missing_logs` = ?,
                
                `activation_email_template` = ?,
                `lost_password_email_template` = ?,
                `credentials_email_template` = ?,

                `admin_email_notification_emails` = ?,
                `admin_new_user_email_notification` = ?,
                `admin_new_payment_email_notification` = ?,
                
                `proxy` = ?,
                `proxy_exclusive` = ?,
                `proxy_timeout` = ?,
                `proxy_failed_requests_pause` = ?,
                `proxy_pause_duration` = ?,                
                
                `email_reports` = ?,
                `email_reports_default` = ?,
                `email_reports_frequency` = ?,
                `email_reports_favorites` = ?
            WHERE `id` = 1
        ");
        $stmt->bind_param('sssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss',
            $_POST['title'],
            $_POST['default_language'],
            $_POST['meta_description'],
            $_POST['meta_keywords'],
            $_POST['time_zone'],
            $_POST['email_confirmation'],
            $_POST['directory'],
            $_POST['directory_pagination'],

            $_POST['store_paypal_client_id'],
            $_POST['store_paypal_secret'],
            $_POST['store_paypal_mode'],
            $_POST['store_stripe_publishable_key'],
            $_POST['store_stripe_secret_key'],
            $_POST['store_stripe_webhook_secret'],
            $_POST['store_currency'],
            $_POST['store_unlock_report_price'],
            $_POST['store_unlock_report_time'],
            $_POST['store_no_ads_price'],
            $_POST['store_user_default_points'],

            $_POST['report_ad'],
            $_POST['index_ad'],
            $_POST['account_sidebar_ad'],
            $_POST['recaptcha'],
            $_POST['public_key'],
            $_POST['private_key'],
            $_POST['facebook_login'],
            $_POST['facebook_app_id'],
            $_POST['facebook_app_secret'],
            $_POST['instagram_login'],
            $_POST['instagram_client_id'],
            $_POST['instagram_client_secret'],
            $_POST['analytics_code'],
            $_POST['facebook'],
            $_POST['twitter'],
            $_POST['youtube'],
            $_POST['instagram'],
            $_POST['smtp_host'],
            $_POST['smtp_port'],
            $_POST['smtp_encryption'],
            $_POST['smtp_auth'],
            $_POST['smtp_user'],
            $_POST['smtp_pass'],
            $_POST['smtp_from'],

            $_POST['cron_queries'],
            $_POST['cron_mode'],
            $_POST['cron_auto_add_missing_logs'],

            $activation_email_template,
            $lost_password_email_template,
            $credentials_email_template,

            $_POST['admin_email_notification_emails'],
            $_POST['admin_new_user_email_notification'],
            $_POST['admin_new_payment_email_notification'],

            $_POST['proxy'],
            $_POST['proxy_exclusive'],
            $_POST['proxy_timeout'],
            $_POST['proxy_failed_requests_pause'],
            $_POST['proxy_pause_duration'],

            $_POST['email_reports'],
            $_POST['email_reports_default'],
            $_POST['email_reports_frequency'],
            $_POST['email_reports_favorites']
        );
        $stmt->execute();
        $stmt->close();

        foreach($plugins->plugins as $plugin_identifier => $value) {
            if($plugins->exists_and_active($plugin_identifier)) {
                require_once $plugins->require($plugin_identifier, 'controllers/admin/website_settings');
            }
        }

        /* Refresh data */
        $settings = get_settings();

        /* Set message */
        $_SESSION['success'][] = $language->admin_website_settings->success_message->saved;

    }

}
