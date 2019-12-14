<?php
defined('MAHIDCODE') || die();
User::check_permission(0);

$reports_count = $database->query("SELECT  COUNT(*) AS `total` FROM `unlocked_reports` LEFT JOIN `instagram_users` ON `unlocked_reports`.`source_user_id` = `instagram_users`.`id` WHERE `user_id` = {$account_user_id}")->fetch_object()->total;
$favorites_count = $database->query("SELECT COUNT(*) AS `total` FROM `favorites` WHERE `user_id` = {$account_user_id}")->fetch_object()->total;