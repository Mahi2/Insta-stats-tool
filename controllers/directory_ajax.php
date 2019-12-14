<?php
defined('MAHIDCODE') || die();

if($settings->directory == 'DISABLED') redirect();

$error = [];

if(!isset($_POST['global_form_token']) || isset($_POST['global_form_token']) && !Security::csrf_check_session_token('global_form_token', $_POST['global_form_token'])) {
    $error[] = $language->global->error_message->invalid_token;
}

/* Response */
if(!empty($error)) {
    Response::json($error, 'error');
    die();
}

/* Get the reports */
$start = (int) filter_var($_POST['start'], FILTER_SANITIZE_NUMBER_INT);
$real_limit = (int) filter_var($_POST['limit'], FILTER_SANITIZE_NUMBER_INT);
$limit = $real_limit + 1;

/* Parse the filters */
$where = '`is_private` = 0 ';
$order_by_column = '`id`';
$order_by_criteria = 'ASC';

$bio = false;
$followers_from = $followers_to = false;
$engagement_from = $engagement_to = false;
$order_by_filter = $order_by_type =  false;


if(isset($_POST['filters'])) {
