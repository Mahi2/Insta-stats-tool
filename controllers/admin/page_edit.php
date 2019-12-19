<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$page_id = (isset($parameters[0])) ? (int) $parameters[0] : false;

/* Check if page exists */
if(!$page = Database::get('*', 'pages', ['page_id' => $page_id])) {
    $_SESSION['error'][] = $language->admin_page_edit->error_message->invalid_page;
    User::get_back('admin/pages-management');
}