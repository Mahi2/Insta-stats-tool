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