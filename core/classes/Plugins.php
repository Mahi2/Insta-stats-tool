<?php

class Plugins {
    public $plugins = [];

    public function __construct() {

        /* Get all the plugins and store their details */
        $result = Database::$database->query("SELECT * FROM `plugins`");

        while($row = $result->fetch_object()) {
            if(file_exists(ROOT . PLUGINS_ROUTE . $row->identifier)) {
                $this->plugins[$row->identifier] = $row;
            } else {
                $this->plugins[$row->identifier] = false;
            }
        }

    }

    public function get($identifier) {

        if(array_key_exists($identifier, $this->plugins)) {
            return $this->plugins[$identifier];
        } else {

            $plugin = Database::get('*', 'plugins', ['identifier' => $identifier]);

            if($plugin && file_exists(ROOT . PLUGINS_ROUTE . $plugin->identifier)) {
                $this->plugins[$plugin->identifier] = $plugin;

                return $this->plugins[$plugin->identifier];
            } else {

                $this->plugins[$identifier] = false;

                return false;

            }
        }

    }

    public function exists_and_active($identifier) {

        $plugin = $this->get($identifier);

        if($plugin) {

            return $this->plugins[$identifier]->status ? $this->plugins[$identifier] : false;

        } else {
            return false;
        }

    }

    public function require($identifier, $path, $extension = '.php') {

        if($plugin = $this->exists_and_active($identifier)) {

            return ROOT . PLUGINS_ROUTE . $plugin->identifier . '/' . $path . $extension;

        } else {

            throw new Exception($identifier . ' plugin does not exist or it is not active.');

        }


    }
}
