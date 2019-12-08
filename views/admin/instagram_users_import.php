<?php defined('ALTUMCODE') || die() ?>

<div class="card card-shadow mb-3">
    <div class="card-body">


        <h4 class="d-flex justify-content-between">
            <span><i class="fa fa-cloud-upload-alt"></i> <?= $language->admin_instagram_users_import->header ?></span>
            <small><?= User::generate_go_back_button('admin/proxies-management') ?></small>
        </h4>
        <p class="text-muted"><?= $language->admin_instagram_users_import->header_help ?></p>
        <form action="" method="post" role="form">
            <div class="form-group">
                <label><?= $language->admin_instagram_users_import->input->content ?></label>
                <textarea name="content" class="form-control" rows="10" placeholder=""></textarea>
                <small class="text-muted"><?= $language->admin_instagram_users_import->input->content_help ?></small>
            </div>

            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary"><?= $language->global->submit_button ?></button>
            </div>
        </form>
        </div>
</div>