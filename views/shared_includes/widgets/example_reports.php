<?php
defined('ALTUMCODE') || die();

foreach($plugins->plugins as $plugin_identifier => $value) {
    if($plugins->exists_and_active($plugin_identifier)) {
        require_once $plugins->require($plugin_identifier, 'views/shared_includes/widgets/example_reports');
    }
}
