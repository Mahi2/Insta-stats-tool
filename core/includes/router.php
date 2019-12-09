<?php

/* Get the current url parameters */
$url = parse_url_parameters();

/* Determine the route */
$route = '';
$route_key = '';

if(isset($url[0])) {

    if($url[0] == 'admin') {
        $route = 'admin/';
        $route_key = 'admin';

        unset($url[0]);
    }

}

/* Process the page that needs to be shown */
$asked_controller = (isset($url[key($url)])) ? htmlspecialchars(current($url), ENT_QUOTES) : 'index';

/* Custom pages names */
$custom_controllers = [
    '' => [
        'account-settings'		=>	'account_settings',
        'paypal'		        =>	'paypal',
        'stripe'		        =>	'stripe',
        'lost-password'			=>	'lost_password',
        'reset-password'		=>  'reset_password',
        'resend-activation'		=>	'resend_activation',
        'my-reports'            =>  'my_reports',
        'api-documentation'     =>  'api_documentation',
        'get-captcha'           =>  'get_captcha',
    ],

    'admin' => [
        'payments-list'		        =>	'payments_list',
        'users-management'		    =>	'users_management',
        'user-edit'				    =>	'user_edit',
        'user-view'				    =>	'user_view',
        'source-users-management'   =>  'source_users_management',
        'instagram-users-import'    =>  'instagram_users_import',
        'pages-management'		    =>	'pages_management',
        'proxies-management'	    =>	'proxies_management',
        'page-edit'				    =>	'page_edit',
        'proxy-edit'				=>	'proxy_edit',
        'photos-management'		    =>	'photos_management',
        'website-settings'		    =>	'website_settings',
        'website-statistics'	    =>	'website_statistics',
        'extra-settings'            =>  'extra_settings'
    ]
];

/* Determine if the current page has a custom url and change it if needed */
$controller = $asked_controller;

if(array_key_exists($asked_controller, $custom_controllers[$route_key])) {
    $controller = $custom_controllers[$route_key][$asked_controller];
}


/* Get all the available pages in the specific route */
$available_controllers = glob(CONTROLLERS_ROUTE . $route . '*.php');
foreach($available_controllers as $key => $value) {
    $available_controllers[$key] = basename($value, '.php');
}

/* Determine if the page is available or not */
if(!in_array($controller, $available_controllers)) {
    $route = '';
    $route_key = '';
    $controller = 'not_found';
}

/* Get the rest of the parameters, if any */
unset($url[key($url)]);
$parameters = $url ? array_values($url) : [];

/* Default variable to set view for page, can be overwritten */
$controller_has_view = true;
$controller_has_container = true;
