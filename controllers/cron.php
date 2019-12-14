<?php
defined('MAHIDCODE') || die();

/* Initiation */
set_time_limit(0);

/* *****************/
/* Unlocked reports validity */
/* *****************/

/* Make sure to clean unlocked reports which are expired */
$database->query("DELETE FROM `unlocked_reports` WHERE `expiration_date` < NOW() AND `expiration_date` <> 0");

/* *****************/
/* Email reporting */
/* *****************/

if($settings->email_reports) {

    switch ($settings->email_reports_frequency) {
        case 'DAILY':
            $timestampdiff = 'DAY';
            $timestampdiff_compare = '0';
            break;

        case 'WEEKLY':
            $timestampdiff = 'DAY';
            $timestampdiff_compare = '6';

            break;

        case 'MONTHLY':
            $timestampdiff = 'MONTH';
            $timestampdiff_compare = '0';

            break;
    }