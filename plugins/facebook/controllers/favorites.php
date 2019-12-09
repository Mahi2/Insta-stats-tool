<?php
defined('ALTUMCODE') || die();

$source_users = [];

$result = $database->query("SELECT `facebook_users`.* FROM `facebook_users` LEFT JOIN `favorites` ON `favorites`.`source_user_id` = `facebook_users`.`id` WHERE `favorites`.`user_id` = {$account_user_id} AND `source` = 'FACEBOOK'");

while($row = $result->fetch_object()) $source_users[] = $row;
