<?php
defined('ALTUMCODE') || die();

$stmt = $database->prepare("
    UPDATE
        `settings`
    SET
        `instagram_check_interval` = ?,
        `instagram_minimum_followers` = ?,
        `instagram_calculator_media_count` = ?
    WHERE `id` = 1
");
$stmt->bind_param('sss',
    $_POST['instagram_check_interval'],
    $_POST['instagram_minimum_followers'],
    $_POST['instagram_calculator_media_count']
);
$stmt->execute();
$stmt->close();
