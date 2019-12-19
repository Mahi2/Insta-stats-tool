<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$proxy_id = (isset($parameters[0])) ? (int) $parameters[0] : false;

/* Check if page exists */
if(!$proxy = Database::get('*', 'proxies', ['proxy_id' => $proxy_id])) {
    $_SESSION['error'][] = $language->admin_proxy_edit->error_message->invalid_page;
    User::get_back('admin/proxies-management');
}

if(!empty($_POST)) {
    /* Filter some the variables */
    $_POST['address'] = Database::clean_string($_POST['address']);
    $_POST['port'] = (int) Database::clean_string($_POST['port']);
    $_POST['username'] = trim(Database::clean_string($_POST['username']));
    $_POST['password'] = trim(Database::clean_string($_POST['password']));
    $_POST['note'] = Database::clean_string($_POST['note']);
    $_POST['method'] = (int) Database::clean_string($_POST['method']);

    $required_fields = ['address', 'port'];

    /* Check for the required fields */
    foreach($_POST as $key=>$value) {
        if(empty($value) && in_array($key, $required_fields)) {
            $_SESSION['error'][] = $language->global->error_message->empty_fields;
            break 1;
        }
    }

    if(!Security::csrf_check_session_token('form_token', $_POST['form_token'])) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }

    /* Select a random instagram user report and check for a result */
    $source_account = $database->query("SELECT `username` FROM `instagram_users` ORDER BY RAND() LIMIT 1")->fetch_object();

    $instagram = new \InstagramScraper\Instagram();
    $instagram->setUserAgent(get_random_user_agent());
    $instagram::setProxy([
        'address' => $_POST['address'],
        'port'    => $_POST['port'],
        'tunnel'  => true,
        'timeout' => $settings->proxy_timeout,
        'auth'    => [
            'user' => $_POST['username'],
            'pass' => $_POST['password'],
            'method' => $_POST['method']
        ]
    ]);

    try {
        $source_account_data = $instagram->getAccount($source_account->username);
    } catch (Exception $error) {
        $_SESSION['error'][] = $error->getMessage();
    }


    if(empty($_SESSION['error'])) {