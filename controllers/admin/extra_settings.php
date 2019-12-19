<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$type 		= isset($parameters[0]) ? $parameters[0] : false;
$id 	    = isset($parameters[1]) ? Database::clean_string($parameters[1]) : false;
$url_token	= isset($parameters[2]) ? $parameters[2] : false;
$source     = isset($parameters[3]) && in_array($parameters[3], $sources) ? $parameters[3] : reset($sources);

if(isset($type) && $type == 'plugin_status') {
    /* Check for errors and permissions */
    if(!Security::csrf_check_session_token('url_token', $url_token)) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }

    if(!$plugin = Database::get(['status'], 'plugins', ['identifier' => $id])) {
        redirect('admin/extra-settings');
    }

    if(empty($_SESSION['error'])) {
        $new_status = (int) !$plugin->status;
        $database->query("UPDATE `plugins` SET `status` = {$new_status} WHERE `identifier` = '{$id}'");

        $_SESSION['success'][] = $language->global->success_message->basic;

        redirect('admin/extra-settings');
    }
}

if(isset($type) && $type == 'demo_delete') {
    /* Check for errors and permissions */
    if(!Security::csrf_check_session_token('url_token', $url_token)) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }

    if(empty($_SESSION['error'])) {

        $table = $source . '_users';

        $database->query("UPDATE {$table} SET `is_demo` = 0 WHERE `id` = {$id}");

        $_SESSION['success'][] = $language->global->success_message->basic;

        redirect('admin/extra-settings');
    }
}

if(!empty($_POST)) {
    if(!empty($_POST['type']) && $_POST['type'] == 'demo_reports') {
        $_POST['username']  = Database::clean_string($_POST['username']);
        $_POST['is_featured'] = (int) $_POST['is_featured'];
        $_POST['source']    = in_array($_POST['source'], $sources) ? Database::clean_string($_POST['source']) : reset($sources);
        $last_checked_date  = (new \DateTime())->modify('-1 years')->format($language->global->date->datetime_format . ' H:i:s');

        if(!Security::csrf_check_session_token('form_token', $_POST['form_token'])) {
            $_SESSION['error'][] = $language->global->error_message->invalid_token;
        }

        if(empty($_SESSION['error'])) {

            $table = $_POST['source'] . '_users';
            $column = $_POST['source'] != 'youtube' ? 'username' : 'youtube_id';

            if($exists = Database::exists($column, $table, [$column => $_POST['username']])) {

                $database->query("UPDATE `{$table}` SET `is_demo` = '1', `is_featured` = '{$_POST['is_featured']}' WHERE `{$column}` = '{$_POST['username']}'");

            } else {

                switch($_POST['source']) {

                    case 'instagram':
                        $sql = "INSERT INTO `instagram_users` (`username`, `full_name`, `description`, `added_date`, `last_check_date`, `is_demo`, `is_featured`) VALUES ('{$_POST['username']}', '{$language->report->state->not_checked_full_name}', '{$language->report->state->not_checked_description}', '{$date}', '{$last_checked_date}', '1', '{$_POST['is_featured']}')";
                        break;

                    case 'facebook':
                        if($plugins->exists_and_active('facebook')) {
                            $sql = "INSERT INTO `facebook_users` (`username`, `name`, `added_date`, `last_check_date`, `is_demo`, `is_featured`) VALUES ('{$_POST['username']}', '{$language->report->state->not_checked_full_name}', '{$date}', '{$last_checked_date}', '1', '{$_POST['is_featured']}')";
                        }
                        break;

                    case 'youtube':
                        if($plugins->exists_and_active('youtube')) {
                            $sql = "INSERT INTO `youtube_users` (`youtube_id`, `title`, `added_date`, `last_check_date`, `is_demo`, `is_featured`) VALUES ('{$_POST['username']}', '{$language->report->state->not_checked_full_name}', '{$date}', '{$last_checked_date}', '1', '{$_POST['is_featured']}')";
                        }
                        break;

                    case 'twitter':
                        if($plugins->exists_and_active('twitter')) {
                            $sql = "INSERT INTO `twitter_users` (`username`, `full_name`, `added_date`, `last_check_date`, `is_demo`, `is_featured`) VALUES ('{$_POST['username']}', '{$language->report->state->not_checked_full_name}', '{$date}', '{$last_checked_date}', '1', '{$_POST['is_featured']}')";
                        }
                        break;

                }