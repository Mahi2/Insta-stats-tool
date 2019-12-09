<?php

class Security {
    public static $csrf_prefix = 'csrf_';

    /* CSRF Protection for ajax requests */
    public static function csrf_generate_token() {
        return md5(time() + rand());
    }

    public static function csrf_set_session_token($name = 'default', $force = false) {

        /* Only set a new session if its not set already or if its forced new */
        if(!@$_SESSION[self::$csrf_prefix . $name] || $force) {
            $token = self::csrf_generate_token();
            $_SESSION[self::$csrf_prefix . $name] = $token;

            return $token;
        }

    }

    public static function csrf_get_session_token($name = 'default') {
        return (isset($_SESSION[self::$csrf_prefix . $name])) ? $_SESSION[self::$csrf_prefix . $name] : false;
    }

    public static function csrf_check_session_token($name = 'default', $value = false) {
        return (
            (isset($_GET[self::$csrf_prefix . $name]) && $_GET[self::$csrf_prefix . $name] == self::csrf_get_session_token($name)) ||
            (isset($_POST[self::$csrf_prefix . $name]) && $_POST[self::$csrf_prefix . $name] == self::csrf_get_session_token($name)) ||
            ($value && $value == self::csrf_get_session_token($name)) ||
            (isset($_SERVER['HTTP_CSRF_TOKEN_' . strtoupper($name)]) && $_SERVER['HTTP_CSRF_TOKEN_' . strtoupper($name)] == self::csrf_get_session_token($name))
        );
    }

    public static function csrf_page_protection_check($name = 'default', $response = true) {
        global $language;


        if(!self::csrf_check_session_token($name)) {
            if($response) Response::json($language->global->error_message->command_denied, 'error');
            die();
        }
    }

}
