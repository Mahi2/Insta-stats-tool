<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$user_id = (isset($parameters[0])) ? (int) $parameters[0] : false;

/* Check if user exists */
if(!$profile_account = Database::get('*', 'users', ['user_id' => $user_id])) {
    $_SESSION['error'][] = $language->admin_user_edit->error_message->invalid_account;
    User::get_back('admin/users-management');
}

if(!empty($_POST)) {
    /* Filter some the variables */
    $_POST['name']		= filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $_POST['status']	= (int) $_POST['status'];
    $_POST['type']	    = (int) $_POST['type'];
    $_POST['no_ads']	= (int) $_POST['no_ads'];
    $_POST['points']    = (int) $_POST['points'];

    /* Check for any errors */
    if(!Security::csrf_check_session_token('form_token', $_POST['form_token'])) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }

    if(strlen($_POST['name']) < 3 || strlen($_POST['name']) > 32) {
        $_SESSION['error'][] = $language->admin_user_edit->error_message->name_length;
    }
    if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
        $_SESSION['error'][] = $language->admin_user_edit->error_message->invalid_email;
    }

    if(Database::exists('user_id', 'users', ['email' => $_POST['email']]) && $_POST['email'] !== Database::simple_get('email', 'users', ['user_id' => $user_id])) {
        $_SESSION['error'][] = $language->admin_user_edit->error_message->email_exists;
    }

    if(!empty($_POST['new_password']) && !empty($_POST['repeat_password'])) {
        if(strlen(trim($_POST['new_password'])) < 6) {
            $_SESSION['error'][] = $language->admin_user_edit->error_message->short_password;
        }
        if($_POST['new_password'] !== $_POST['repeat_password']) {
            $_SESSION['error'][] = $language->admin_user_edit->error_message->passwords_not_matching;
        }
    }


    if(empty($_SESSION['error'])) {
        /* Update the basic user settings */
        $stmt = $database->prepare("
			UPDATE
				`users`
			SET
				`name` = ?,
				`email` = ?,
				`active` = ?,
				`no_ads` = ?,
				`type` = ?,
				`points` = ?
			WHERE
				`user_id` = {$user_id}
		");
        $stmt->bind_param(
            'ssssss',
            $_POST['name'],
            $_POST['email'],
            $_POST['status'],
            $_POST['no_ads'],
            $_POST['type'],
            $_POST['points']
        );
        $stmt->execute();
        $stmt->close();