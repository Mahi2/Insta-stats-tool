<?php defined('ALTUMCODE') || die() ?>

<div class="card card-shadow">
    <div class="card-body">

        <h4 class="d-flex justify-content-between">
            <div class="d-flex">
                <span class="mr-3"><?= $language->admin_user_view->header ?></span>

                <?= User::admin_generate_buttons('user', $profile_account->user_id) ?>
            </div>

            <div><?= User::generate_go_back_button('admin/users-management') ?></div>
        </h4>

        <div class="row mt-md-3">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $language->admin_user_view->input->username ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $profile_account->username ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $language->admin_user_view->input->name ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $profile_account->name ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $language->admin_user_view->input->email ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $profile_account->email ?>" readonly />
                </div>
            </div>
        </div>

        <div class="row mt-md-3">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $language->admin_user_view->input->last_activity ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $profile_account->last_activity ? (new \DateTime($profile_account->last_activity))->format($language->global->date->datetime_format . ' H:i:s') : '-' ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $language->admin_user_view->input->status ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $profile_account->active ? $language->admin_user_view->input->status_active : $language->admin_user_view->input->status_disabled ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $language->admin_user_view->input->no_ads ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $profile_account->no_ads ? $language->global->yes : $language->global->no ?>" readonly />
                </div>
            </div>
        </div>


        <div class="row mt-md-3">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $language->admin_user_view->input->points ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $profile_account->points ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $language->admin_user_view->input->api_key ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $profile_account->api_key ?>" readonly />
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold"><?= $language->admin_user_view->input->email_reports ?></label>
                    <input type="text" class="form-control-plaintext" value="<?= $profile_account->email_reports ? $language->global->yes : $language->global->no ?>" readonly />
                </div>
            </div>
        </div>

    </div>
</div>

<div class="card mt-3 card-shadow">
    <div class="card-body">
        <h4><?= $language->admin_user_view->header_reports ?></h4>
        <div><?php printf($language->admin_user_view->subheader_reports) ?></div>

        <div class="my-3"></div>

        <?php if($profile_reports->num_rows): ?>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th><?= $language->admin_user_view->table->nr ?></th>
                        <th></th>
                        <th><?= $language->admin_user_view->table->username ?></th>
                        <th><?= $language->admin_user_view->table->date ?></th>
                        <th><?= $language->admin_user_view->table->expiration_date ?></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php $nr = 1; while($row = $profile_reports->fetch_object()): ?>
                        <?php if(!in_array($row->source, $sources)) continue ?>

                        <tr>
                            <td class="text-muted"><?= $nr++ ?></td>
                            <td><i class="<?= $language->{$row->source}->global->icon ?> text-<?= $row->source ?>"></i> <?= $language->{$row->source}->global->name ?></td>
                            <td><a href="report/<?= $row->username ?>/<?= $row->source ?>" data-toggle="tooltip" title="<?= $row->full_name ?>"><?= $row->source == 'instagram' ? '@' . $row->username : $row->username ?></a></td>
                            <td><span><?= (new DateTime($row->date))->format($language->global->date->datetime_format) ?></span></td>
                            <td>
                                <?php if($row->expiration_date == '0'): ?>
                                    <?= $language->admin_user_view->table->no_expiration_date ?>
                                <?php else: ?>
                                    <span data-toggle="tooltip" title="<?= $row->expiration_date ?>"><?= (new DateTime($row->expiration_date))->format($language->global->date->datetime_format) ?></span>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endwhile ?>

                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <?= $language->admin_user_view->info_message->no_unlocked_reports ?>
        <?php endif ?>
    </div>
</div>

<div class="card mt-3 card-shadow">
    <div class="card-body">
        <h4><?= $language->admin_user_view->header_favorites ?></h4>
        <div><?php printf($language->admin_user_view->subheader_favorites) ?></div>

        <div class="my-3"></div>

        <?php if($profile_favorites->num_rows): ?>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th><?= $language->admin_user_view->table->nr ?></th>
                        <th></th>
                        <th><?= $language->admin_user_view->table->username ?></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php $nr = 1; while($row = $profile_favorites->fetch_object()): ?>
                        <?php if(!in_array($row->source, $sources)) continue ?>

                        <tr>
                            <td class="text-muted"><?= $nr++ ?></td>
                            <td><i class="<?= $language->{$row->source}->global->icon ?> text-<?= $row->source ?>"></i> <?= $language->{$row->source}->global->name ?></td>
                            <td><a href="report/<?= $row->username ?>/<?= $row->source ?>" data-toggle="tooltip" title="<?= $row->username ?>"><?= $row->full_name ?></a></td>
                        </tr>
                    <?php endwhile ?>

                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <?= $language->admin_user_view->info_message->no_favorites ?>
        <?php endif ?>
    </div>
</div>

<div class="card card-shadow mt-3">
    <div class="card-body">

        <h4><?= $language->admin_user_view->header_transactions ?></h4>

        <?php if($profile_transactions->num_rows): ?>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th><?= $language->admin_user_view->table->nr ?></th>
                        <th><?= $language->admin_user_view->table->type ?></th>
                        <th><?= $language->admin_user_view->table->email ?></th>
                        <th><?= $language->admin_user_view->table->name ?></th>
                        <th><?= $language->admin_user_view->table->amount ?></th>
                        <th><?= $language->admin_user_view->table->date ?></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php $nr = 1; while($row = $profile_transactions->fetch_object()): ?>
                        <tr>
                            <td class="text-muted"><?= $nr++ ?></td>
                            <td>
                                <span data-toggle="tooltip" title="<?= $row->type ?>">
                                    <i class="fab fa-<?= strtolower($row->type) ?>"></i>
                                </span>
                            </td>
                            <td><?= $row->email ?></td>
                            <td><?= $row->name ?></td>
                            <td><span class="text-success"><?= $row->amount ?></span> <?= $row->currency ?></td>
                            <td><span data-toggle="tooltip" title="<?= $row->date ?>"><?= (new DateTime($row->date))->format($language->global->date->datetime_format) ?></span></td>
                        </tr>
                    <?php endwhile ?>

                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <?= $language->admin_user_view->info_message->no_transactions ?>
        <?php endif ?>

    </div>
</div>