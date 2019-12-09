<?php
defined('ALTUMCODE') || die();

$source_users = [];

$result = $database->query("SELECT `twitter_users`.* FROM `twitter_users` LEFT JOIN `favorites` ON `favorites`.`source_user_id` = `twitter_users`.`id` WHERE `favorites`.`user_id` = {$account_user_id} AND `source` = 'TWITTER'");

while($row = $result->fetch_object()) $source_users[] = $row;
