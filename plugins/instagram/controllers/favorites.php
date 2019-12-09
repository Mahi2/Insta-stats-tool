<?php
defined('ALTUMCODE') || die();

$source_users = [];

$result = $database->query("SELECT `instagram_users`.* FROM `instagram_users` LEFT JOIN `favorites` ON `favorites`.`source_user_id` = `instagram_users`.`id` WHERE `favorites`.`user_id` = {$account_user_id} AND `source` = 'INSTAGRAM'");

while($row = $result->fetch_object()) $source_users[] = $row;
