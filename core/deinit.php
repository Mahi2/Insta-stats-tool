<?php
defined('ALTUMCODE') || die();

if(MYSQL_DEBUG) {
    $result = $database->query("show profiles");

    while($profile = $result->fetch_object()) {
        echo $profile->Query_ID . ' - ' . round($profile->Duration, 4) * 1000 . ' ms - ' . $profile->Query . '<br />';
    }
}

$database->close();
ob_flush();
