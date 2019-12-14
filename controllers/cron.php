<?php
defined('MAHIDCODE') || die();

/* Initiation */
set_time_limit(0);

/* *****************/
/* Unlocked reports validity */
/* *****************/

/* Make sure to clean unlocked reports which are expired */
$database->query("DELETE FROM `unlocked_reports` WHERE `expiration_date` < NOW() AND `expiration_date` <> 0");
