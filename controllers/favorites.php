<?php
defined('MAHIDCODE') || die();
User::check_permission(0);

$source = isset($parameters[0]) && in_array($parameters[0], $sources) ? Database::clean_string($parameters[0]) : reset($sources);

/* In the case that someone adds or removes from favorites */
if(!empty($_POST)) {
    $source = in_array($_POST['source'], $sources) ? Database::clean_string($_POST['source']) : reset($sources);

    /* We need to check if the favorite already exists and remove it or add it */
    if($id = Database::simple_get('id', 'favorites', ['user_id' => $account_user_id, 'source' => $source, 'source_user_id' => $_POST['source_user_id']])) {
        $database->query("DELETE FROM `favorites` WHERE `id` = '{$id}'");
        Response::json('unfavorited', 'success', ['html' => $language->report->display->add_favorite]);
        die();
    } else {
        Database::insert('favorites', ['user_id' => $account_user_id, 'source' => $source, 'source_user_id' => $_POST['source_user_id']]);
        Response::json('favorited', 'success', ['html' => $language->report->display->remove_favorite]);
        die();
    }
}

if($plugins->exists_and_active($source)) {
    require_once $plugins->require($source, 'controllers/favorites');
}