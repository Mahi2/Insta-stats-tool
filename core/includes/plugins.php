<?php
defined('ALTUMCODE') || die();

$sources = [];

if($plugins->exists_and_active('instagram')) {
    $sources[] = 'instagram';
}

if($plugins->exists_and_active('facebook')) {
    $sources[] = 'facebook';
}

if($plugins->exists_and_active('youtube')) {
    $sources[] = 'youtube';
}

if($plugins->exists_and_active('twitter')) {
    $sources[] = 'twitter';
}

/* Check for additions to the language */
foreach($plugins->plugins as $plugin_identifier => $value) {
    if($plugins->exists_and_active($plugin_identifier) && file_exists($plugins->require($plugin_identifier, 'languages/' . $lang, '.json'))) {

        $language->{$plugin_identifier} = json_decode(file_get_contents($plugins->require($plugin_identifier, 'languages/' . $lang, '.json')));

        /* Check the language file */
        if(is_null($language->{$plugin_identifier})) {
            die('The language file for ' . $plugin_identifier . ' plugin is corrupted. Please make sure your JSON Language file is JSON Validated.');
        }

    }

}

