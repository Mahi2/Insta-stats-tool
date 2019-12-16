<?php
defined('MAHIDCODE') || die();
User::logged_in_redirect();

$email = (isset($parameters[0])) ? $parameters[0] : false;
$lost_password_code = (isset($parameters[1])) ? $parameters[1] : false;

if(!$email || !$lost_password_code) redirect();

/* Check if the lost password code is correct */
$user_id = Database::simple_get('user_id', 'users', ['email' => $email, 'lost_password_code' => $lost_password_code]);

if($user_id < 1 || strlen($lost_password_code) < 1) redirect();

if(!empty($_POST)) {
    /* Check for any errors */
    if(strlen(trim($_POST['new_password'])) < 6) {
        $_SESSION['error'][] = $language->reset_password->error_message->short_password;
    }
    if($_POST['new_password'] !== $_POST['repeat_password']) {
        $_SESSION['error'][] = $language->reset_password->error_message->passwords_not_matching;
    }

    if(empty($_SESSION['error'])) {
        /* Encrypt the new password */
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        /* Update the password & empty the reset code from the database */
        $stmt = $database->prepare("UPDATE `users` SET `password` = ?, `lost_password_code` = 0  WHERE `user_id` = ?");
        $stmt->bind_param('ss', $new_password, $user_id);
        $stmt->execute();
        $stmt->close();

        /* Store success message */
        $_SESSION['success'][] = $language->reset_password->success_message->password_updated;

        redirect();
    }

}