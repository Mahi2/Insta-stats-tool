<?php
defined('ALTUMCODE') || die();

$source_users = [];

$result = $database->query("
    SELECT  
        `unlocked_reports`.`date`, 
        `unlocked_reports`.`source_user_id`,
        `unlocked_reports`.`user_id`, 
        `unlocked_reports`.`expiration_date`, 
        `twitter_users`.`username`, 
        `twitter_users`.`full_name`,
        `twitter_users`.`followers`,
        `twitter_users`.`following`,
        `twitter_users`.`tweets`,
        `twitter_users`.`likes`,
        `twitter_users`.`profile_picture_url`
    FROM `unlocked_reports` 
    LEFT JOIN `twitter_users` ON `unlocked_reports`.`source_user_id` = `twitter_users`.`id` 
    WHERE 
        `user_id` = {$account_user_id}
        AND `source` = 'TWITTER'
");

while($row = $result->fetch_object()) $source_users[] = $row;

$source_users_csv = csv_exporter($source_users, ['date', 'source_user_id', 'user_id', 'expiration_date']);
