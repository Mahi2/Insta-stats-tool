<?php
defined('ALTUMCODE') || die();

$source_users = [];

$result = $database->query("
    SELECT  
        `unlocked_reports`.`date`, 
        `unlocked_reports`.`source_user_id`,
        `unlocked_reports`.`user_id`, 
        `unlocked_reports`.`expiration_date`, 
        `instagram_users`.`username`, 
        `instagram_users`.`full_name`, 
        `instagram_users`.`average_engagement_rate`,
        `instagram_users`.`followers`, 
        `instagram_users`.`profile_picture_url`,
        `instagram_users`.`uploads` 
    FROM `unlocked_reports` 
    LEFT JOIN `instagram_users` ON `unlocked_reports`.`source_user_id` = `instagram_users`.`id` 
    WHERE 
        `user_id` = {$account_user_id}
        AND `source` = 'INSTAGRAM'
");

while($row = $result->fetch_object()) $source_users[] = $row;

$source_users_csv = csv_exporter($source_users, ['date', 'source_user_id', 'user_id', 'expiration_date']);
