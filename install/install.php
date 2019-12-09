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
/* Success check */
if($response->body->status == 'success') {

    /* Prepare the config file content */
    $config_content =
<<<ALTUM
<?php
defined('ALTUMCODE') || die();

\$config = [
    'database_host'        => '{$_POST['database_host']}',
    'database_username'    => '{$_POST['database_username']}',
    'database_password'    => '{$_POST['database_password']}',
    'database_name'        => '{$_POST['database_name']}',
    'url'                  => '{$_POST['url']}'
];

ALTUM;

    /* Write the new config file */
    file_put_contents(ROOT . 'core/config/config.php', $config_content);

    /* Run SQL */
    $dump_content = file_get_contents(ROOT . 'install/dump.sql');

    $dump = explode('-- SEPARATOR --', $dump_content);

    foreach($dump as $query) {
        $database->query($query);
    }

    die(json_encode([
        'status' => 'success',
        'message' => ''
    ]));
}
