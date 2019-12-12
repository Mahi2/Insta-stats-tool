<?php
defined('MAHIDCODE') || die();

$source = isset($parameters[0]) && in_array($parameters[0], $sources) ? Database::clean_string($parameters[0]) : reset($sources);
$user_one = isset($parameters[1]) ? Database::clean_string($parameters[1]) : false;
$user_two = isset($parameters[2]) ? Database::clean_string($parameters[2]) : false;

$table = $source . '_users';
$column = $source != 'youtube' ? 'username' : 'youtube_id';

/* We need to check if the user already exists in our database */
switch($source) {
    case 'youtube':

        $stmt = $database->prepare("SELECT * FROM `youtube_users` WHERE `youtube_id` = ? OR `username` = ?");
        $stmt->bind_param('ss', $user_one, $user_one);
        $stmt->execute();
        $result = $stmt->get_result();
        $source_account_one = $result->fetch_object();
        $stmt->close();

        $stmt = $database->prepare("SELECT * FROM `youtube_users` WHERE `youtube_id` = ? OR `username` = ?");
        $stmt->bind_param('ss', $user_two, $user_two);
        $stmt->execute();
        $result = $stmt->get_result();
        $source_account_two = $result->fetch_object();
        $stmt->close();

        break;

    default:
        $source_account_one = $user_one ? Database::get('*', $table, ['username' => $user_one]) : false;
        $source_account_two = $user_two ? Database::get('*', $table, ['username' => $user_two]) : false;

}

/* Check if the searched accounts are existing to the database */
if($user_one && !$source_account_one) {
    $_SESSION['info'][] = sprintf($language->compare->info_message->user_not_found, $user_one, $user_one, $user_one);
}

if($user_two && !$source_account_two) {
    $_SESSION['info'][] = sprintf($language->compare->info_message->user_not_found, $user_two, $user_two, $user_two);
}
