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

    /* Check for other errors and permissions */
    if(!in_array($package, $allowed_packages)) {
        $_SESSION['error'][] = $language->store->error_message->allowed_packages;
    }
    if(!Security::csrf_check_session_token('url_token', $url_token)) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }
    if($account->points < $price) {
        $_SESSION['info'][] = $language->store->error_message->not_enough_funds;
    }

    if(empty($_SESSION['error']) && empty($_SESSION['info'])) {
        /* Doing the necessary database changes to the database */
        switch($package) {

            /* Unlock report process */
            case 'unlock_report' :
                $expiration_date = ($settings->store_unlock_report_time != 0) ? (new \DateTime())->modify('+'.$settings->store_unlock_report_time.' days')->format('Y-m-d H:i:s') : 0;

                Database::insert(
                    'unlocked_reports',
                    [
                        'user_id'           => $account_user_id,
                        'source'            => $source,
                        'source_user_id'    => $source_user_id,
                        'date'              => $date,
                        'expiration_date'   => $expiration_date
                    ]
                );

                Database::update(
                    'users',
                    [
                        'points' => $account->points - $price
                    ],
                    ['user_id' => $account_user_id]
                );

                /* Display a success message */
                $_SESSION['success'][] = $language->store->success_message->report_unlocked;

                /* Redirect to the report page */
                redirect('report/' . $username . '/' . $source);

            break;

            /* No ads Package */
            case 'no-ads' :

                Database::update(
                    'users',
                    [
                        'points' => $account->points - $price,
                        'no_ads' => '1'
                    ],
                    ['user_id' => $account_user_id]
                );

                /* Update the account variable */
                $account = Database::get('*', 'users', ['user_id' => $account_user_id]);


                /* Display a success message */
                $_SESSION['success'][] = $language->store->success_message->purchased;

            break;

        }

    }

}

Security::csrf_set_session_token('url_token');