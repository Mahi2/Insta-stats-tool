<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$type 		= (isset($parameters[0])) ? $parameters[0] : false;
$page_id 	= (isset($parameters[1])) ? (int) $parameters[1] : false;
$url_token	= (isset($parameters[2])) ? $parameters[2] : false;

if(isset($type) && $type == 'delete') {
    /* Check for errors and permissions */
	if(!Security::csrf_check_session_token('url_token', $url_token)) {
		$_SESSION['error'][] = $language->global->error_message->invalid_token;
	}


	if(empty($_SESSION['error'])) {
		$database->query("DELETE FROM `pages` WHERE `page_id` = {$page_id}");

		$_SESSION['success'][] = $language->global->success_message->basic;
	}

}

if(!empty($_POST)) {
	/* Filter some the variables */
	$_POST['title'] = Database::clean_string($_POST['title']);

    if(strpos($_POST['url'], 'http://') !== false || strpos($_POST['url'], 'https://') !== false) {
        $_POST['url']	= Database::clean_string($_POST['url']);
    } else {
        $_POST['url']	= generate_slug(Database::clean_string($_POST['url']), '-');
    }

	$_POST['position'] = (in_array($_POST['position'], ['1', '0'])) ? $_POST['position'] : '0';
    $_POST['description'] = addslashes($_POST['description']);

	$required_fields = ['title', 'url'];

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

    /* If there are no errors continue the updating process */
	if(empty($_SESSION['error'])) {
		$database->query("INSERT INTO `pages` (`title`, `url`, `description`, `position`) VALUES ('{$_POST['title']}', '{$_POST['url']}', '{$_POST['description']}', '{$_POST['position']}')");

		$_SESSION['success'][] = $language->global->success_message->basic;
	}

}

$pages_result = $database->query("SELECT * FROM `pages` ORDER BY `page_id` ASC");