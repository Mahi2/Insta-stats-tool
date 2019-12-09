<?php
defined('ALTUMCODE') || die();

/* Set the Languages directory */
$path = ROOT . 'languages/';

/* Establish the current language variable */
$lang = $settings->default_language;

/* Determine all the langauges available in the directory */
$unprocessed_files = glob($path . '*.json');
$files = [];
foreach($unprocessed_files as $file) {
	$file = explode('/', $file);
	$files[] = end($file);
}

$languages = preg_replace('(.json)', '', $files);

/* If the cookie is set and the language file exists, override the default language */
if(isset($_COOKIE['language']) && in_array($_COOKIE['language'], $languages)) $lang = $_COOKIE['language'];

/* Check if the language wants to be checked via the GET variable */
if(isset($_GET['language'])) {
	$_GET['language'] = filter_var($_GET['language'], FILTER_SANITIZE_STRING);

	/* Check if the requested language exists and set it if needed */
	if(in_array($_GET['language'], $languages)) {
		setcookie('language', $_GET['language'], time()+60*60*24*3);
		$lang = $_GET['language'];
	}
}

/* Include the language file */
$language = json_decode(file_get_contents($path . $lang . '.json'));

/* Check the language file */
if(is_null($language)) {
    die('The language file is corrupted. Please make sure your JSON Language file is JSON Validated.');
}
