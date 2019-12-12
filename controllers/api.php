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

if($settings->store_unlock_report_price != '0') {
    /* Make sure the API key is correct */
    $profile_account = Database::get(['user_id', 'type'], 'users', ['api_key' => $_GET['api_key']]);

    if(!$profile_account) {
        echo json_encode(['access' => false, 'message' => $language->api->error_message->unauthorized]); die();
    }

    /* Make sure the username exists and the user has access to it */
    if(!User::has_valid_report($source_user_id, $profile_account->user_id, $source) && $profile_account->type != '1') {
        echo json_encode(['access' => false, 'message' => $language->api->error_message->unauthorized]); die();
    }
}

$data = Database::get('*', $table, ['id' => $source_user_id]);

/* Remove not needed data*/
unset($data->id);
unset($data->is_demo);

$data->details = json_decode($data->details);
$data->access = true;

if($source == 'instagram') {
    require_once $plugins->require($source, 'controllers/api');
}

/* Output the json content */
echo json_encode($data);

$controller_has_view = false;