<?php
defined('ALTUMCODE') || die();

$source_users = [];

$result = $database->query("
    SELECT  
        `unlocked_reports`.`date`, 
        `unlocked_reports`.`source_user_id`,
        `unlocked_reports`.`user_id`, 
        `unlocked_reports`.`expiration_date`, 
        `facebook_users`.`username`, 
        `facebook_users`.`name`, 
        `facebook_users`.`likes`,
        `facebook_users`.`followers`,
        `facebook_users`.`profile_picture_url`
    FROM `unlocked_reports` 
    LEFT JOIN `facebook_users` ON `unlocked_reports`.`source_user_id` = `facebook_users`.`id` 
    WHERE 
        `user_id` = {$account_user_id}
        AND `source` = 'FACEBOOK'
");

while($row = $result->fetch_object()) $source_users[] = $row;

$source_users_csv = csv_exporter($source_users, ['date', 'source_user_id', 'user_id', 'expiration_date']);
