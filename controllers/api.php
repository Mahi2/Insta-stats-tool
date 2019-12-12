<?php
defined('MAHIDCODE') || die();

/* Define the return content to be treated as JSON */
header('Content-Type: application/json');

/* Make sure we have the needed params */
if(!isset($_GET['username'], $_GET['api_key'])) {
    http_response_code(403); die();
}

$source = isset($_GET['source']) && in_array($_GET['source'], $sources) ? Database::clean_string($_GET['source']) : reset($sources);

$table = $source . '_users';
$column = $_GET['source'] != 'youtube' ? 'username' : 'youtube_id';

if(!$source_user_id = Database::simple_get('id', $table, [$column => $_GET['username']])) {
    echo json_encode(['access' => false, 'message' => $language->api->error_message->not_found]);  die();
}