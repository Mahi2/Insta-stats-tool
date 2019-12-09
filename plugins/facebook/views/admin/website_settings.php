<?php defined('ALTUMCODE') || die() ?>

<h5><i class="fab fa-facebook"></i> Facebook</h5>

<div class="form-group">
    <label><?= $language->facebook->admin_website_settings->input->facebook_check_interval ?></label>
    <input type="number" min="1" name="facebook_check_interval" class="form-control" value="<?= $settings->facebook_check_interval ?>" />
    <small class="text-muted"><?= $language->facebook->admin_website_settings->input->facebook_check_interval_help ?></small>
</div>

<div class="form-group">
    <label><?= $language->facebook->admin_website_settings->input->facebook_minimum_likes ?></label>
    <input type="number" min="0" name="facebook_minimum_likes" class="form-control" value="<?= $settings->facebook_minimum_likes ?>" />
    <small class="text-muted"><?= $language->facebook->admin_website_settings->input->facebook_minimum_likes_help ?></small>
</div>

