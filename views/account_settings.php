<?php defined('ALTUMCODE') || die() ?>

<div class="row">
    <div class="col-md-8 mb-3 mb-md-0">

        <div class="card card-shadow">
            <div class="card-body">

                <div class="d-flex justify-content-between">
                    <h4><?= $language->account_settings->header ?></h4>

                    <small class="text-muted"><?= $language->account_settings->display->last_activity ?> <?= (new DateTime($account->last_activity))->format($language->global->date->datetime_format . ' H:i:s') ?></small>
                </div>

                <form action="" method="post" role="form">
                    <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />

                    <div class="form-group">
                        <label for="username"><?= $language->account_settings->input->username ?></label>
                        <input type="text" id="username" name="username" class="form-control" value="<?= $account->username ?>" />
                    </div>

                    <div class="form-group">
                        <label for="name"><?= $language->account_settings->input->name ?></label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= $account->name ?>" />
                    </div>

                    <div class="form-group">
                        <label for="email"><?= $language->account_settings->input->email ?></label>
                        <input type="text" id="email" name="email" class="form-control" value="<?= $account->email ?>" />
                    </div>

                    <?php if($settings->email_reports): ?>
                    <hr class="my-4"/>

                    <div class="form-group">
                        <label for="email_reports"><?= $language->account_settings->input->email_reports ?></label>

                        <select class="custom-select" id="email_reports" name="email_reports">
                            <option value="1" <?php if($account->email_reports == 1) echo 'selected' ?>><?= $language->global->yes ?></option>
                            <option value="0" <?php if($account->email_reports == 0) echo 'selected' ?>><?= $language->global->no ?></option>
                        </select>
                        <small class="form-text text-muted"><?= sprintf($language->account_settings->input->email_reports_help, $language->global->date->{strtolower($settings->email_reports_frequency)}) ?></small>
                    </div>
                    <?php endif ?>

                    <hr class="my-4"/>

                    <h5><?= $language->account_settings->header2 ?></h5>
                    <small class="text-muted"><?= $language->account_settings->header2_help ?></small>

                    <div class="form-group">
                        <label for="old_password"><?= $language->account_settings->input->current_password ?></label>
                        <input type="password" id="old_password" name="old_password" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="new_password"><?= $language->account_settings->input->new_password ?></label>
                        <input type="password" id="new_password" name="new_password" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="repeat_password"><?= $language->account_settings->input->repeat_password ?></label>
                        <input type="password" id="repeat_password" name="repeat_password" class="form-control" />
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" name="submit" class="btn btn-primary"><?= $language->global->submit_button ?></button>
                    </div>

                </form>


            </div>
        </div>


        <div class="card card-shadow mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5><?= $language->account_settings->header3 ?></h5>
                        <p class="text-muted"><?= $language->account_settings->header3_help ?></p>
                    </div>

                    <a href="account_settings/delete/<?=  Security::csrf_get_session_token('url_token') ?>" class="btn btn-danger" data-confirm="<?= $language->global->info_message->confirm_delete ?>"><?= $language->global->delete ?></a>
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-4">
        <?php include VIEWS_ROUTE . 'shared_includes/widgets/sidebar.php' ?>
    </div>
</div>



