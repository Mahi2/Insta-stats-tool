<?php defined('ALTUMCODE') || die() ?>

<h5><i class="fab fa-instagram"></i> Instagram</h5>

<div class="form-group">
    <label><?= $language->instagram->admin_website_settings->input->instagram_check_interval ?></label>
    <input type="number" min="1" name="instagram_check_interval" class="form-control" value="<?= $settings->instagram_check_interval ?>" />
    <small class="text-muted"><?= $language->instagram->admin_website_settings->input->instagram_check_interval_help ?></small>
</div>

<div class="form-group">
    <label><?= $language->instagram->admin_website_settings->input->instagram_minimum_followers ?></label>
    <input type="number" min="0" name="instagram_minimum_followers" class="form-control" value="<?= $settings->instagram_minimum_followers ?>" />
    <small class="text-muted"><?= $language->instagram->admin_website_settings->input->instagram_minimum_followers_help ?></small>
</div>

<div class="form-group">
    <label><?= $language->instagram->admin_website_settings->input->instagram_calculator_media_count ?></label>
    <input type="number" max="30" name="instagram_calculator_media_count" class="form-control" value="<?= $settings->instagram_calculator_media_count ?>" />
    <small class="text-muted"><?= $language->instagram->admin_website_settings->input->instagram_calculator_media_count_help ?></small>
</div>
