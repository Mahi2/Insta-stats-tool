<?php defined('ALTUMCODE') || die() ?>

<h5><i class="fab fa-twitter"></i> Twitter</h5>

<div class="form-group">
    <label><?= $language->twitter->admin_website_settings->input->twitter_consumer_key ?></label>
    <input type="text" name="twitter_consumer_key" class="form-control" value="<?= $settings->twitter_consumer_key ?>" />
    <small class="text-muted"><?= $language->twitter->admin_website_settings->input->twitter_consumer_key_help ?></small>
</div>

<div class="form-group">
    <label><?= $language->twitter->admin_website_settings->input->twitter_secret_key ?></label>
    <input type="text" name="twitter_secret_key" class="form-control" value="<?= $settings->twitter_secret_key ?>" />
</div>

<div class="form-group">
    <label><?= $language->twitter->admin_website_settings->input->twitter_oauth_token ?></label>
    <input type="text" name="twitter_oauth_token" class="form-control" value="<?= $settings->twitter_oauth_token ?>" />
</div>

<div class="form-group">
    <label><?= $language->twitter->admin_website_settings->input->twitter_oauth_token_secret ?></label>
    <input type="text" name="twitter_oauth_token_secret" class="form-control" value="<?= $settings->twitter_oauth_token_secret ?>" />
</div>

<div class="form-group">
    <label><?= $language->twitter->admin_website_settings->input->twitter_check_interval ?></label>
    <input type="number" min="1" name="twitter_check_interval" class="form-control" value="<?= $settings->twitter_check_interval ?>" />
    <small class="text-muted"><?= $language->twitter->admin_website_settings->input->twitter_check_interval_help ?></small>
</div>

<div class="form-group">
    <label><?= $language->twitter->admin_website_settings->input->twitter_minimum_followers ?></label>
    <input type="number" min="0" name="twitter_minimum_followers" class="form-control" value="<?= $settings->twitter_minimum_followers ?>" />
    <small class="text-muted"><?= $language->twitter->admin_website_settings->input->twitter_minimum_followers_help ?></small>
</div>

<div class="form-group">
    <label><?= $language->twitter->admin_website_settings->input->twitter_check_tweets ?></label>
    <input type="number" min="0" name="twitter_check_tweets" class="form-control" value="<?= $settings->twitter_check_tweets ?>" />
    <small class="text-muted"><?= $language->twitter->admin_website_settings->input->twitter_check_tweets_help ?></small>
</div>
