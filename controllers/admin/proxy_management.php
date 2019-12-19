<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

$type 		= (isset($parameters[0])) ? $parameters[0] : false;
$proxy_id 	= (isset($parameters[1])) ? (int) $parameters[1] : false;
$url_token	= (isset($parameters[2])) ? $parameters[2] : false;

$default_values = [
    'address' => '',
    'port' => '',
    'username' => '',
    'password' => '',
    'note' => ''
];

if(isset($type) && $type == 'delete') {

    /* Check for errors and permissions */
	if(!Security::csrf_check_session_token('url_token', $url_token)) {
		$_SESSION['error'][] = $language->global->error_message->invalid_token;
	}

	if(empty($_SESSION['error'])) {
		$database->query("DELETE FROM `proxies` WHERE `proxy_id` = {$proxy_id}");

		$_SESSION['success'][] = $language->global->success_message->basic;
	}

    redirect('admin/proxies-management');
}

if(isset($type) && $type == 'test') {

    $proxy = Database::get('*', 'proxies', ['proxy_id' => $proxy_id]);

    /* Check for errors and permissions */
    if(!Security::csrf_check_session_token('url_token', $url_token)) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }

    /* Select a random instagram user report and check for a result */
    $source_account = $database->query("SELECT `username` FROM `instagram_users` ORDER BY RAND() LIMIT 1")->fetch_object();

    if(!$source_account) {
        $source_account = (object) ['username' => 'g_eazy'];
    }

    $instagram = new \InstagramScraper\Instagram();
    $instagram->setUserAgent(get_random_user_agent());
    $instagram::setProxy([
        'address' => $proxy->address,
        'port'    => $proxy->port,
        'tunnel'  => true,
        'timeout' => $settings->proxy_timeout,
        'auth'    => [
            'user' => $proxy->username,
            'pass' => $proxy->password,
            'method' => $proxy->method
        ]
    ]);

    try {
        $source_account_data = $instagram->getAccount($source_account->username);
    } catch (Exception $error) {
        $_SESSION['error'][] = $error->getMessage();
    }

    if(empty($_SESSION['error'])) {
        $_SESSION['success'][] = $language->admin_proxies_management->success_message->test;
    }

    redirect('admin/proxies-management');
}

if(!empty($_POST)) {

    /* Filter some the variables */
	$_POST['address'] = Database::clean_string($_POST['address']);
    $_POST['port'] = (int) Database::clean_string($_POST['port']);
    $_POST['username'] = trim(Database::clean_string($_POST['username']));
    $_POST['password'] = trim(Database::clean_string($_POST['password']));
    $_POST['note'] = Database::clean_string($_POST['note']);
    $_POST['method'] = (int) Database::clean_string($_POST['method']);

    $default_values = [
        'address' => $_POST['address'],
        'port' => $_POST['port'],
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'note' => $_POST['note']
    ];

	$required_fields = ['address', 'port'];

	/* Check for the required fields */
	foreach($_POST as $key => $value) {
		if(empty($value) && in_array($key, $required_fields)) {
			$_SESSION['error'][] = $language->global->error_message->empty_fields;
			break 1;
		}
	}

    if(!Security::csrf_check_session_token('form_token', $_POST['form_token'])) {
        $_SESSION['error'][] = $language->global->error_message->invalid_token;
    }

    /* Select a random instagram user report and check for a result */
    $source_account = $database->query("SELECT `username` FROM `instagram_users` ORDER BY RAND() LIMIT 1")->fetch_object();

    if(!$source_account) {
        $source_account = (object) ['username' => 'g_eazy'];
    }

    $instagram = new \InstagramScraper\Instagram();
    $instagram->setUserAgent(get_random_user_agent());
    $instagram::setProxy([
        'address' => $_POST['address'],
        'port'    => $_POST['port'],
        'tunnel'  => true,
        'timeout' => $settings->proxy_timeout,
        'auth'    => [
            'user' => $_POST['username'],
            'pass' => $_POST['password'],
            'method' => $_POST['method']
        ]
    ]);

    try {
        $source_account_data = $instagram->getAccount($source_account->username);
    } catch (Exception $error) {
        $_SESSION['error'][] = $error->getMessage();
    }

    /* If there are no errors continue the updating process */
	if(empty($_SESSION['error'])) {
		$database->query("INSERT INTO `proxies` (`address`, `port`, `username`, `password`, `note`, `method`, `date`) VALUES ('{$_POST['address']}', '{$_POST['port']}', '{$_POST['username']}', '{$_POST['password']}', '{$_POST['note']}', '{$_POST['method']}', '{$date}')");

		$_SESSION['success'][] = $language->global->success_message->basic;

        redirect('admin/proxies-management');
    }

}

$proxies_result = $database->query("SELECT * FROM `proxies`");