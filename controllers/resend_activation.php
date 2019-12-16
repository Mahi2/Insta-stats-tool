<?php
defined('MAHIDCODE') || die();
User::logged_in_redirect();

$email = '';

/* Initiate captcha */
$captcha = new Captcha($settings->recaptcha, $settings->public_key, $settings->private_key);

if(!empty($_POST)) {
    /* Clean the posted variable */
    $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $email = $_POST['email'];

    /* Check for any errors */
    if(!$captcha->is_valid()) {
        $_SESSION['error'][] = $language->global->error_message->invalid_captcha;
    }

    /* If there are no errors, resend the activation link */
    if(empty($_SESSION['error'])) {
        $this_account = Database::get(['user_id', 'active', 'name', 'email', 'username'], 'users', ['email' => $_POST['email']]);

        if($this_account && !(bool) $this_account->active) {
            /* Generate new email code */
            $email_code = md5($_POST['email'] . microtime());

            /* Update the current activation email */
            $database->query("UPDATE `users` SET `email_activation_code` = '{$email_code}' WHERE `user_id` = {$this_account->user_id}");

            /* Prepare the email */
            $email_template = generate_email_template(
                [
                    '{{NAME}}' => $this_account->name,
                    '{{WEBSITE_TITLE}}' => $settings->title
                ],
                $settings->activation_email_template_subject,
                [
                    '{{ACTIVATION_LINK}}' => $settings->url . 'activate/' . md5($this_account->email) . '/' . $email_code,
                    '{{NAME}}' => $this_account->name,
                    '{{ACCOUNT_USERNAME}}' => $this_account->username,
                    '{{WEBSITE_TITLE}}' => $settings->title
                ],
                $settings->activation_email_template_body
            );

            /* Send the email */
            sendmail($_POST['email'], $email_template->subject, $email_template->body);

        }

        /* Store success message */
        $_SESSION['success'][] = $language->resend_activation->notice_message->success;
    }


}