<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-3">
    <a href="admin/instagram-users-import" class="btn btn-primary"><i class="fa fa-cloud-upload-alt"></i> <?= $language->instagram->admin_users_management->display->instagram_users_import ?></a>
</div>

<div class="card card-shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table id="results" class="table">
                <thead class="thead-black">
                    <tr>
                        <th><?= $language->instagram->admin_users_management->table->username ?></th>
                        <th><?= $language->instagram->admin_users_management->table->full_name ?></th>
                        <th><?= $language->instagram->admin_users_management->table->last_check_date ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<input type="hidden" name="url" value="<?= $settings->url . $route . 'source-users-management/instagram/ajax' ?>" />
