<?php
defined('MAHIDCODE') || die();

$user = isset($parameters[0]) ? Database::clean_string($parameters[0]) : false;
$source = isset($parameters[1]) && in_array($parameters[1], $sources) ? Database::clean_string($parameters[1]) : reset($sources);
$date_start = isset($parameters[2]) ? Database::clean_string($parameters[2]) : false;
$date_end = isset($parameters[3]) ? Database::clean_string($parameters[3]) : false;
$date_string = ($date_start && $date_end && validate_date($date_start, 'Y-m-d') && validate_date($date_end, 'Y-m-d')) ? $date_start . ',' . $date_end : false;

$refresh = isset($_GET['refresh']) && Security::csrf_check_session_token('url_token', $_GET['refresh']);

if(!$user || ($source !== 'instagram' && !$plugins->exists_and_active($source))) redirect();

$is_proxy_request = false;

/* Check if we need to use a proxy */
if($settings->proxy) {

    /* Select a proxy from the database */
    $proxy = $database->query("
        SELECT *
        FROM `proxies`
        WHERE
            (`failed_requests` < {$settings->proxy_failed_requests_pause})
            OR
            (`failed_requests` >= {$settings->proxy_failed_requests_pause} AND '{$date}' > DATE_ADD(`last_date`, INTERVAL {$settings->proxy_pause_duration} MINUTE))
        ORDER BY `last_date` ASC
    ");

    if($proxy->num_rows) {

        $proxy = $proxy->fetch_object();

        $rand = rand(1, 10);

        /* Give it a 50 - 50 percent chance to choose from the server or from the proxy in case the proxy is not exclusive */
        if($settings->proxy_exclusive || (!$settings->proxy_exclusive && $rand > 5)) {

            $is_proxy_request = [
                'address' => $proxy->address,
                'port'    => $proxy->port,
                'tunnel'  => true,
                'timeout' => $settings->proxy_timeout,
                'auth'    => [
                    'user' => $proxy->username,
                    'pass' => $proxy->password,
                    'method' => $proxy->method
                ]
            ];

        }

    }

}

require_once $plugins->require($source, 'controllers/report');

/* Some extra processing */
$source_account->last_check_date = (new \DateTime($source_account->last_check_date))->format($language->global->date->datetime_format . ' H:i:s');
$source_account->last_successful_check_date = (new \DateTime($source_account->last_successful_check_date))->format($language->global->date->datetime_format . ' H:i:s');

/* Check if current user has a valid report */
$has_valid_report = User::logged_in() && User::has_valid_report($source_account->id, $account_user_id, $source);

/* Get favorites data */
if(User::logged_in()) {
    $is_favorited = Database::simple_get('id', 'favorites', [
        'user_id' => $account_user_id,
        'source_user_id' => $source_account->id
    ]);
}

Security::csrf_set_session_token('url_token', true);

$controller_has_container = false;

/* Insert the needed libraries */
add_event('head', function() {
    global $settings;
    global $source_account;
    global $source;

    echo '<link href="' . $settings->url . ASSETS_ROUTE . 'css/datepicker.min.css" rel="stylesheet" media="screen,print">';
    echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/datepicker.min.js"></script>';
    echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/i18n/datepicker.en.js"></script>';
    echo '<script src="' . $settings->url . ASSETS_ROUTE . 'js/Chart.bundle.min.js"></script>';

    /* Canonical for SEO purposes */
    echo '<link rel="canonical" href="' . $settings->url . 'report/' . $source_account->username . '/' . $source . '" />';

});