<?php
defined('ALTUMCODE') || die();

$source_users = [];

$result = $database->query("SELECT `youtube_users`.* FROM `youtube_users` LEFT JOIN `favorites` ON `favorites`.`source_user_id` = `youtube_users`.`id` WHERE `favorites`.`user_id` = {$account_user_id} AND `source` = 'YOUTUBE'");

while($row = $result->fetch_object()) $source_users[] = $row;
