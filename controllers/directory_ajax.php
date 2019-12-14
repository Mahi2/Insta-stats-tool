<?php
defined('MAHIDCODE') || die();

if($settings->directory == 'DISABLED') redirect();

$error = [];

if(!isset($_POST['global_form_token']) || isset($_POST['global_form_token']) && !Security::csrf_check_session_token('global_form_token', $_POST['global_form_token'])) {
    $error[] = $language->global->error_message->invalid_token;
}
