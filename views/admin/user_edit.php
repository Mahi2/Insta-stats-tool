<?php defined('ALTUMCODE') || die() ?>

<div class="card card-shadow">
    <div class="card-body">

        <h4 class="d-flex justify-content-between">
            <div class="d-flex">
                <span class="mr-3"><?= $language->admin_user_edit->header ?></span>

                <?= User::admin_generate_buttons('user', $profile_account->user_id) ?>
            </div>

            <div><?= User::generate_go_back_button('admin/users-management') ?></div>
        </h4>

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->username ?></label>
                <input type="text" class="form-control" value="<?= $profile_account->username ?>" disabled="true"/>
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->last_activity ?></label>
                <input type="text" class="form-control" value="<?= $profile_account->last_activity ? (new \DateTime($profile_account->last_activity))->format($language->global->date->datetime_format . ' H:i:s') : '-' ?>" disabled="true" />
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->name ?></label>
                <input type="text" name="name" class="form-control" value="<?= $profile_account->name ?>" />
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->email ?></label>
                <input type="text" name="email" class="form-control" value="<?= $profile_account->email ?>" />
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->status ?></label>

                <select class="custom-select" name="status">
                    <option value="1" <?php if($profile_account->active == 1) echo 'selected' ?>><?= $language->admin_user_edit->input->status_active ?></option>
                    <option value="0" <?php if($profile_account->active == 0) echo 'selected' ?>><?= $language->admin_user_edit->input->status_disabled ?></option>
                </select>
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->no_ads . ' ( <em>' . $language->admin_user_edit->input->no_ads_help . '</em> )' ?></label>

                <select class="custom-select" name="no_ads">
                    <option value="1" <?php if($profile_account->no_ads == 1) echo 'selected' ?>><?= $language->global->yes ?></option>
                    <option value="0" <?php if($profile_account->no_ads == 0) echo 'selected' ?>><?= $language->global->no ?></option>
                </select>
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->type . ' ( <em>' . $language->admin_user_edit->input->type_help . '</em> )' ?></label>

                <select class="custom-select" name="type">
                    <option value="1" <?php if($profile_account->type == 1) echo 'selected' ?>><?= $language->admin_user_edit->input->type_admin ?></option>
                    <option value="0" <?php if($profile_account->type == 0) echo 'selected' ?>><?= $language->admin_user_edit->input->type_user ?></option>
                </select>
            </div>


            <div class="form-group">
                <label><?= $language->admin_user_edit->input->points ?></label>
                <input type="text" name="points" class="form-control" value="<?= $profile_account->points ?>" />
            </div>

            <h4 class="mt-5"><?= $language->admin_user_edit->header_password ?></h4>
            <p class="text-muted"><?= $language->admin_user_edit->subheader_password ?></p>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->new_password ?></label>
                <input type="password" name="new_password" class="form-control" />
            </div>

            <div class="form-group">
                <label><?= $language->admin_user_edit->input->repeat_password ?></label>
                <input type="password" name="repeat_password" class="form-control" />
            </div>


            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary mt-5"><?= $language->global->submit_button ?></button>
            </div>
        </form>
    </div>
</div>