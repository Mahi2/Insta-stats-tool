<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$user_id = (isset($parameters[0])) ? (int) $parameters[0] : false;

/* Check if user exists */
if(!$profile_account = Database::get('*', 'users', ['user_id' => $user_id])) {
    $_SESSION['error'][] = $language->admin_user_edit->error_message->invalid_account;
    User::get_back('admin/users-management');
}

$profile_transactions = $database->query("SELECT * FROM `payments` WHERE `user_id` = {$user_id} ORDER BY `id` DESC");

/* Get the profile unlocked reports */
$sql = "
    SELECT 'instagram' AS `source`, `unlocked_reports`.`date`, `unlocked_reports`.`expiration_date`, `instagram_users`.`username`, `instagram_users`.`full_name` FROM `unlocked_reports` LEFT JOIN `instagram_users` ON `unlocked_reports`.`source_user_id` = `instagram_users`.`id` WHERE `unlocked_reports`.`source` = 'INSTAGRAM' AND `user_id` = {$user_id}
    UNION SELECT 'twitter' AS `source`, `unlocked_reports`.`date`, `unlocked_reports`.`expiration_date`, `twitter_users`.`username`, `twitter_users`.`full_name` FROM `unlocked_reports` LEFT JOIN `twitter_users` ON `unlocked_reports`.`source_user_id` = `twitter_users`.`id` WHERE `unlocked_reports`.`source` = 'TWITTER' AND `user_id` = {$user_id}
";

if($plugins->exists_and_active('facebook')) {
    $sql .= "UNION SELECT 'facebook' AS `source`, `unlocked_reports`.`date`, `unlocked_reports`.`expiration_date`, `facebook_users`.`username`, `facebook_users`.`name` as `full_name` FROM `unlocked_reports` LEFT JOIN `facebook_users` ON `unlocked_reports`.`source_user_id` = `facebook_users`.`id` WHERE `unlocked_reports`.`source` = 'FACEBOOK' AND `user_id` = {$user_id}";
}

if($plugins->exists_and_active('youtube')) {
    $sql .= "UNION SELECT 'youtube' AS `source`, `unlocked_reports`.`date`, `unlocked_reports`.`expiration_date`, `youtube_users`.`youtube_id` AS `username`, `youtube_users`.`title` AS `full_name` FROM `unlocked_reports` LEFT JOIN `youtube_users` ON `unlocked_reports`.`source_user_id` = `youtube_users`.`id` WHERE `unlocked_reports`.`source` = 'YOUTUBE' AND `user_id` = {$user_id}";
}

$profile_reports = $database->query($sql);

/* Get the profile favorite reports */
$sql = "
    SELECT 'instagram' AS `source`, `instagram_users`.`full_name`, `instagram_users`.`username` FROM `instagram_users` LEFT JOIN `favorites` ON `favorites`.`source_user_id` = `instagram_users`.`id` WHERE `favorites`.`source` = 'INSTAGRAM' AND `favorites`.`user_id` = {$user_id}
    UNION SELECT 'twitter' AS `source`, `twitter_users`.`full_name`, `twitter_users`.`username` FROM `twitter_users` LEFT JOIN `favorites` ON `favorites`.`source_user_id` = `twitter_users`.`id` WHERE `favorites`.`source` = 'TWITTER' AND `favorites`.`user_id` = {$user_id}
";

if($plugins->exists_and_active('facebook')) {
    $sql .= "UNION SELECT 'facebook' AS `source`, `facebook_users`.`name` as `full_name`, `facebook_users`.`username` FROM `facebook_users` LEFT JOIN `favorites` ON `favorites`.`source_user_id` = `facebook_users`.`id` WHERE `favorites`.`source` = 'FACEBOOK' AND `favorites`.`user_id` = {$user_id}";
}

if($plugins->exists_and_active('youtube')) {
    $sql .= "UNION SELECT 'youtube' AS `source`, `youtube_users`.`title` AS `full_name`, `youtube_users`.`youtube_id` AS `username` FROM `youtube_users` LEFT JOIN `favorites` ON `favorites`.`source_user_id` = `youtube_users`.`id` WHERE `favorites`.`source` = 'YOUTUBE' AND `favorites`.`user_id` = {$user_id}";
}

$profile_favorites = $database->query($sql);