<?php defined('ALTUMCODE') || die() ?>

<div class="card card-shadow mb-3">
    <div class="card-body">


        <h4 class="d-flex justify-content-between">
            <span><i class="fa fa-cloud-upload-alt"></i> <?= $language->admin_instagram_users_import->header ?></span>
            <small><?= User::generate_go_back_button('admin/proxies-management') ?></small>
        </h4>
        <p class="text-muted"><?= $language->admin_instagram_users_import->header_help ?></p>