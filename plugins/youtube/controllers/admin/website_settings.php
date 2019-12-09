<?php
defined('ALTUMCODE') || die();

$stmt = $database->prepare("
    UPDATE
        `settings`
    SET
        `youtube_api_key` = ?,
        `youtube_check_interval` = ?,
        `youtube_minimum_subscribers` = ?,
        `youtube_check_videos` = ?
    WHERE `id` = 1
");
$stmt->bind_param('ssss',
    $_POST['youtube_api_key'],
    $_POST['youtube_check_interval'],
    $_POST['youtube_minimum_subscribers'],
    $_POST['youtube_check_videos']
);
$stmt->execute();
$stmt->close();
