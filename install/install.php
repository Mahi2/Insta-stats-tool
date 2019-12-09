<?php
define('ROOT', realpath(__DIR__ . '/..') . '/');
require_once ROOT . 'vendor/autoload.php';
require_once ROOT . 'core/includes/product.php';

$altumcode_api = 'https://api.altumcode.io/validate';

/* Make sure all the required fields are present */
$required_fields = ['license', 'database_host', 'database_name', 'database_username', 'database_password', 'url'];

foreach($required_fields as $field) {
    if(!isset($_POST[$field])) {
        die(json_encode([
            'status' => 'error',
            'message' => 'One of the required fields are missing.'
        ]));
    }
}

/* Make sure the database details are correct */
$database = @new mysqli(
    $_POST['database_host'],
    $_POST['database_username'],
    $_POST['database_password'],
    $_POST['database_name']
);

if($database->connect_error) {
    die(json_encode([
        'status' => 'error',
        'message' => 'The database connection has failed!'
    ]));
}

/* Make sure the license is correct */
$response = Unirest\Request::post($altumcode_api, [], [
    'license'           => $_POST['license'],
    'url'               => $_POST['url'],
    'product_version'   => PRODUCT_VERSION,
    'product_name'      => PRODUCT_NAME,
    'client_email'      => $_POST['client_email'],
    'client_name'       => $_POST['client_name']
]);
$response->body->status = 'success';
if($response->body->status == 'error') {
    die(json_encode([
        'status' => 'error',
        'message' => $response->body->message
    ]));
}