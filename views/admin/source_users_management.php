<?php
defined('ALTUMCODE') || die();
User::check_permission(1);

require_once $plugins->require($source, 'views/admin/users_management');