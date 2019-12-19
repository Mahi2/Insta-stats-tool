<?php
defined('MAHIDCODE') || die();
User::check_permission(1);

if(!empty($_POST)) {
    $content = preg_split("/(\,|\n)/", $_POST['content']);
    $last_checked_date = (new \DateTime())->modify('-1 years')->format($language->global->date->datetime_format . ' H:i:s');
    $total_inserts = 0;
    $total_duplicates = 0;