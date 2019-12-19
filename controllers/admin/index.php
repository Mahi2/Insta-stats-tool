<?php
defined('ALTUMCODE') || die();
User::check_permission(1);

$payments = $database->query("SELECT COUNT(*) AS `transactions`, IFNULL(TRUNCATE(SUM(`amount`), 2), 0) AS `earnings` FROM `payments` WHERE `currency` = '{$settings->store_currency}'")->fetch_object();
$reports = $database->query("SELECT COUNT(*) AS `unlocked_reports` FROM `unlocked_reports`")->fetch_object();
$users = $database->query("
    SELECT
      (SELECT COUNT(*) FROM `users` WHERE MONTH(`last_activity`) = MONTH(CURRENT_DATE()) AND YEAR(`last_activity`) = YEAR(CURRENT_DATE())) AS `active_users_month`,
      (SELECT COUNT(*) FROM `users`) AS `active_users`
")->fetch_object();