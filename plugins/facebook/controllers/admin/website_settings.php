<?php
defined('ALTUMCODE') || die();

$stmt = $database->prepare("
    UPDATE
        `settings`
    SET
        `facebook_check_interval` = ?,
        `facebook_minimum_likes` = ?
    WHERE `id` = 1
");
$stmt->bind_param('ss',
$_POST['facebook_check_interval'],
$_POST['facebook_minimum_likes']
);
$stmt->execute();
$stmt->close();
