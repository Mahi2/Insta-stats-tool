<?php defined('ALTUMCODE') || die() ?>

<h5><i class="fab fa-youtube"></i> YouTube</h5>

<div class="form-group">
    <label><?= $language->youtube->admin_website_settings->input->youtube_api_key ?></label>
    <input type="text" name="youtube_api_key" class="form-control" value="<?= $settings->youtube_api_key ?>" />
    <small class="text-muted"><?= $language->youtube->admin_website_settings->input->youtube_api_key_help ?></small>
</div>

<div class="form-group">
    <label><?= $language->youtube->admin_website_settings->input->youtube_check_interval ?></label>
    <input type="number" min="1" name="youtube_check_interval" class="form-control" value="<?= $settings->youtube_check_interval ?>" />
    <small class="text-muted"><?= $language->youtube->admin_website_settings->input->youtube_check_interval_help ?></small>
</div>

<div class="form-group">
    <label><?= $language->youtube->admin_website_settings->input->youtube_minimum_subscribers ?></label>
    <input type="number" min="0" name="youtube_minimum_subscribers" class="form-control" value="<?= $settings->youtube_minimum_subscribers ?>" />
    <small class="text-muted"><?= $language->youtube->admin_website_settings->input->youtube_minimum_subscribers_help ?></small>
</div>

<div class="form-group">
    <label><?= $language->youtube->admin_website_settings->input->youtube_check_videos ?></label>
    <input type="number" min="0" max="50" name="youtube_check_videos" class="form-control" value="<?= $settings->youtube_check_videos ?>" />
    <small class="text-muted"><?= $language->youtube->admin_website_settings->input->youtube_check_videos_help ?></small>
</div>
