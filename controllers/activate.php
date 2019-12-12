<?php
defined('MAHIDCODE') || die();

$md5_email = (isset($parameters[0])) ? $parameters[0] : false;
$email_activation_code = (isset($parameters[1])) ? $parameters[1] : false;

if(!$md5_email || !$email_activation_code) redirect();

/* Get email of user so we can further compare */
$this_account = Database::get(['user_id', 'email'], 'users', ['email_activation_code' => $email_activation_code]);

if(!$this_account) redirect();

/* Make sure the email that has been sent is correct also */
if(md5($this_account->email) != $md5_email) redirect();

/* Activate the account and reset the email_activation_code */
$stmt = $database->prepare("UPDATE `users` SET `active` = 1, `email_activation_code` = '' WHERE `user_id` = ?");
$stmt->bind_param('s', $this_account->user_id);
$stmt->execute();
$stmt->close();