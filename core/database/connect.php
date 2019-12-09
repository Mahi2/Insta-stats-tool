<?php

/* Establishing the connection */
$database = new mysqli(
    $config['database_host'],
    $config['database_username'],
    $config['database_password'],
    $config['database_name']
);

/* Debugging */
if($database->connect_error) {
    die('The connection to the database failed!');
}

/* Encoding */
$database->set_charset('utf8mb4');

/* Initiate the Database Class */
Database::$database = $database;
