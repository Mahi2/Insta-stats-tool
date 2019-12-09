<?php
defined('ALTUMCODE') || die();

$stmt = $database->prepare("
    UPDATE
        `settings`
    SET
        `twitter_consumer_key` = ?,
        `twitter_secret_key` = ?,
        `twitter_oauth_token` = ?,
        `twitter_oauth_token_secret` = ?,
        `twitter_check_interval` = ?,
        `twitter_minimum_followers` = ?,
        `twitter_check_tweets` = ?
    WHERE `id` = 1
");
$stmt->bind_param('sssssss',
    $_POST['twitter_consumer_key'],
    $_POST['twitter_secret_key'],
    $_POST['twitter_oauth_token'],
    $_POST['twitter_oauth_token_secret'],
    $_POST['twitter_check_interval'],
    $_POST['twitter_minimum_followers'],
    $_POST['twitter_check_tweets']
);
$stmt->execute();
$stmt->close();
