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

    /* Check for any errors */
    if(!Security::csrf_check_session_token('form_token', $_POST['form_token'])) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }
    if(strlen($_POST['username']) < 3 || strlen($_POST['username']) > 32) {
        $_SESSION['error'][] = $language->account_settings->error_message->username_length;
    }
    if(Database::exists('user_id', 'users', ['username' => $_POST['username']]) && $_POST['username'] !== $account->username) {
        $_SESSION['error'][] = sprintf($language->account_settings->error_message->user_exists, $_POST['username']);
    }
    if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
        $_SESSION['error'][] = $language->register->error_message->invalid_email;
    }
    if(Database::exists('user_id', 'users', ['email' => $_POST['email']]) && $_POST['email'] !== $account->email) {
        $_SESSION['error'][] = $language->register->error_message->email_exists;
    }

    if(strlen($_POST['name']) < 3 || strlen($_POST['name'] > 32)) {
        $_SESSION['error'][] = $language->register->error_message->name_length;
    }

    if(!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
        if(!password_verify($_POST['old_password'], $account->password)) {
            $_SESSION['error'][] = $language->account_settings->error_message->invalid_current_password;
        }
        if(strlen(trim($_POST['new_password'])) < 6) {
            $_SESSION['error'][] = $language->account_settings->error_message->short_password;
        }
        if($_POST['new_password'] !== $_POST['repeat_password']) {
            $_SESSION['error'][] = $language->account_settings->error_message->passwords_not_matching;
        }
    }



    if(empty($_SESSION['error'])) {
        /* Prepare the statement and execute query */
        $stmt = $database->prepare("UPDATE `users` SET `email` = ?, `username` = ?, `name` = ? WHERE `user_id` = {$account_user_id}");
        $stmt->bind_param('sss', $_POST['email'], $_POST['username'], $_POST['name']);
        $stmt->execute();
        $stmt->close();

        /* Change the email settings too */
        if($settings->email_reports) {
            $stmt = $database->prepare("UPDATE `users` SET `email_reports` = ? WHERE `user_id` = {$account_user_id}");
            $stmt->bind_param('s', $_POST['email_reports']);
            $stmt->execute();
            $stmt->close();
        }

        $_SESSION['success'][] = $language->account_settings->success_message->account_updated;
        $account = Database::get('*', 'users', ['user_id' => $account_user_id]);


        if(!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

            Database::update('users', ['password' => $new_password], ['user_id' => $account_user_id]);