<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$proxy_id = (isset($parameters[0])) ? (int) $parameters[0] : false;

/* Check if page exists */
if(!$proxy = Database::get('*', 'proxies', ['proxy_id' => $proxy_id])) {
    $_SESSION['error'][] = $language->admin_proxy_edit->error_message->invalid_page;
    User::get_back('admin/proxies-management');
}