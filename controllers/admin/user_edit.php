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