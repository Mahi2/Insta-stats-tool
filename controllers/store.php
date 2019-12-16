<?php
defined('MAHIDCODE') || die();
User::check_permission(0);

$package 	= isset($parameters[0]) ? $parameters[0] : false;
$username   = isset($parameters[1]) ? $parameters[1] : false;
$url_token 	= isset($parameters[2]) ? $parameters[2] : false;
$source     = isset($parameters[3]) && in_array($parameters[3], $sources) ? Database::clean_string($parameters[3]) : reset($sources);

$allowed_packages = ['unlock_report', 'no-ads'];

if($package && $username && $url_token) {

    switch($package) {

        case 'unlock_report' :
            $price = $settings->store_unlock_report_price;

            $table = $source . '_users';

            if(!$source_user_id = Database::simple_get('id', $table, ['username' => $username])) {
                redirect('store');
            }
        break;

        case 'no-ads' :
            $price = $settings->store_no_ads_price;

            /* Check for errors specific to the package */
            if($account->no_ads) {
                $_SESSION['error'][] = $language->store->error_message->already_no_ads;
            }
        break;

    }