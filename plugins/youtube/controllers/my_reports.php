<?php
defined('ALTUMCODE') || die();

$source_users = [];

$result = $database->query("
    SELECT  
        `unlocked_reports`.`date`, 
        `unlocked_reports`.`source_user_id`,
        `unlocked_reports`.`user_id`, 
        `unlocked_reports`.`expiration_date`, 
        `youtube_users`.`youtube_id`, 
        `youtube_users`.`title`, 
        `youtube_users`.`subscribers`,
        `youtube_users`.`views`,
        `youtube_users`.`videos`,
        `youtube_users`.`profile_picture_url`
    FROM `unlocked_reports` 
    LEFT JOIN `youtube_users` ON `unlocked_reports`.`source_user_id` = `youtube_users`.`id` 
    WHERE 
        `user_id` = {$account_user_id}
        AND `source` = 'YOUTUBE'
");

while($row = $result->fetch_object()) $source_users[] = $row;

$source_users_csv = csv_exporter($source_users, ['date', 'source_user_id', 'user_id', 'expiration_date']);
