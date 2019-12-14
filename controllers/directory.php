<?php
defined('MAHIDCODE') || die();
if($settings->directory == 'DISABLED' || !$plugins->exists_and_active('instagram')) redirect();

if($settings->directory == 'DISABLED') redirect();

if($settings->directory == 'LOGGED_IN' && !User::logged_in()) {

    $_SESSION['info'][] = $language->directory->info_message->logged_in;

    redirect('login?redirect=directory');

}

$controller_has_container = false;