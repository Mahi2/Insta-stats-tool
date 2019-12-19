<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$page_id = (isset($parameters[0])) ? (int) $parameters[0] : false;

/* Check if page exists */
if(!$page = Database::get('*', 'pages', ['page_id' => $page_id])) {
    $_SESSION['error'][] = $language->admin_page_edit->error_message->invalid_page;
    User::get_back('admin/pages-management');
}

if(!empty($_POST)) {
    /* Filter some the variables */
    if(strpos($_POST['url'], 'http://') !== false || strpos($_POST['url'], 'https://') !== false) {
        $_POST['url']	= Database::clean_string($_POST['url']);
    } else {
        $_POST['url']	= generate_slug(Database::clean_string($_POST['url']), '-');
    }
    $_POST['title'] = Database::clean_string($_POST['title']);
    $_POST['position'] = (in_array($_POST['position'], ['1', '0'])) ? $_POST['position'] : '0';
    $_POST['description'] = addslashes($_POST['description']);

    if(!Security::csrf_check_session_token('form_token', $_POST['form_token'])) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }

    if(empty($_SESSION['error'])) {
        /* Update the database */
        $database->query("UPDATE `pages` SET `title` = '{$_POST['title']}', `url` = '{$_POST['url']}', `description` = '{$_POST['description']}', `position` = '{$_POST['position']}' WHERE `page_id` = {$page_id}");

        /* Set a nice success message */
        $_SESSION['success'][] = $language->global->success_message->basic;

        /* Update the current settings */
        $page = Database::get('*', 'pages', ['page_id' => $page_id]);

    }
}