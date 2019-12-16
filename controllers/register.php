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

    /* If there are no errors continue the registering process */
    if(empty($_SESSION['error'])) {
        /* Define some needed variables */
        $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $active 	= ($settings->email_confirmation == 0) ? '1' : '0';
        $email_code = md5($_POST['email'] . microtime());
        $api_key = md5($_POST['email'].$_POST['username']);

        /* Add the user to the database */
        $stmt = $database->prepare("INSERT INTO `users` (`username`, `password`, `email`, `email_activation_code`, `name`, `active`, `date`, `api_key`, `points`, `email_reports`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssssss', $_POST['username'], $password, $_POST['email'], $email_code, $_POST['name'], $active, $date, $api_key, $settings->store_user_default_points, $settings->email_reports_default);
        $stmt->execute();
        $registered_user_id = $stmt->insert_id;
        $stmt->close();

        /* Send notification to admin if needed */
        if($settings->admin_new_user_email_notification && !empty($settings->admin_email_notification_emails)) {

            sendmail(
                explode(',', $settings->admin_email_notification_emails),
                $language->global->email->admin_new_user_email_notification_subject,
                sprintf($language->global->email->admin_new_user_email_notification_body, $_POST['name'], $_POST['username'])
            );

        }

        /* If active = 1 then login the user, else send the user an activation email */
        if($active == '1') {
            $_SESSION['user_id'] = $registered_user_id;
            $_SESSION['success'] = $language->register->success_message->login;
            redirect($redirect);
        } else {

            /* Prepare the email */
            $email_template = generate_email_template(
                [
                    '{{NAME}}' => $_POST['name'],
                    '{{WEBSITE_TITLE}}' => $settings->title
                ],
                $settings->activation_email_template_subject,
                [
                    '{{ACTIVATION_LINK}}' => $settings->url . 'activate/' . md5($_POST['email']) . '/' . $email_code,
                    '{{NAME}}' => $_POST['name'],
                    '{{ACCOUNT_USERNAME}}' => $_POST['username'],
                    '{{WEBSITE_TITLE}}' => $settings->title
                ],
                $settings->activation_email_template_body
            );

            sendmail($_POST['email'], $email_template->subject, $email_template->body);

            $_SESSION['success'][] = $language->register->success_message->registration;
        }

    }


}


/* Insert the recaptcha library */
add_event('head', function() {
    echo '<script src="https://www.google.com/recaptcha/api.js"></script>';
});