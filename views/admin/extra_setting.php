<?php defined('ALTUMCODE') || die() ?>

<div class="card card-shadow">
    <div class="card-body">
        <h4><?= $language->admin_extra_settings->header2 ?></h4>
        <p class="text-muted"><?= $language->admin_extra_settings->subheader2 ?></p>


        <div class="mt-5">
            <?php if(!$plugins_result->num_rows): ?>

                <?= $language->admin_extra_settings->display->no_plugins ?>

            <?php else: ?>
                <?php while($plugin = $plugins_result->fetch_object()): ?>

                    <div class="media mb-3">
                        <div class="mr-3 plugin" style="background: <?= $plugin->color ?>"><?= substr($plugin->name, 0, 1) ?></div>

                        <div class="media-body">
                            <h6 class="my-0"><?= $plugin->name ?></h6>
                        </div>
                        <div>
                            <?php if($plugins->get($plugin->identifier)): ?>

                                <?php if($plugin->status): ?>
                                    <a href="admin/extra-settings/plugin_status/<?= $plugin->identifier . '/' . Security::csrf_get_session_token('url_token') ?>" class="btn btn-sm btn-primary"><?= $language->admin_extra_settings->display->deactivate ?></a>
                                <?php else: ?>
                                    <a href="admin/extra-settings/plugin_status/<?= $plugin->identifier . '/' . Security::csrf_get_session_token('url_token') ?>" class="btn btn-sm btn-light"><?= $language->admin_extra_settings->display->activate ?></a>
                                <?php endif ?>

                            <?php else: ?>
                                <a href="https://altumcode.link/phpanalyzer" class="btn btn-sm btn-success"><i class="fa fa-cloud-download-alt"></i> <?= $language->admin_extra_settings->display->get ?></a>
                            <?php endif ?>
                        </div>
                    </div>

                <?php endwhile ?>
            <?php endif ?>
        </div>
    </div>
</div>
<div class="card card-shadow mt-3">
    <div class="card-body">

        <h5><?= $language->admin_extra_settings->display->demo_reports ?></h5>
        <p class="text-muted"><?= $language->admin_extra_settings->display->demo_reports_help ?></p>

        <table class="table table-hover">
            <thead class="thead-black thead-inverse">
            <tr>
                <th><?= $language->admin_extra_settings->table->username ?></th>
                <th><?= $language->admin_extra_settings->table->is_featured ?></th>
                <th><?= $language->admin_extra_settings->table->source ?></th>
                <th><?= $language->admin_extra_settings->table->actions ?></th>
            </tr>
            </thead>
            <tbody>

            <?php while($row = $demo_users_result->fetch_object()): ?>
                <?php if(!in_array($row->source, $sources)) continue ?>

                <tr>
                    <td><a href="<?= url('report/' . $row->username . '/' . $row->source) ?>" target="_blank"><?= $row->username ?></td>
                    <td><?= $row->is_featured ? '<span class="badge badge-pill badge-success"><i class="fa fa-check-circle fa-sm"></i> ' . $language->global->yes . '</span>' : '<span class="badge badge-pill badge-info"><i class="fa fa-times-circle fa-sm"></i> ' . $language->global->no . '</span>' ?></td>
                    <td><i class="<?= $language->{$row->source}->global->icon ?> text-<?= $row->source ?>"></i> <?= $language->{$row->source}->global->name ?></td>
                    <td>
                        <div class="dropdown">
                            <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
                                <i class="fas fa-ellipsis-v"></i>

                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" data-confirm="<?= $language->global->info_message->confirm_delete ?>" href="admin/extra-settings/demo_delete/<?= $row->id . '/' . Security::csrf_get_session_token('url_token') . '/' . $row->source  ?>"><i class="fa fa-times"></i>  <?= $language->global->delete ?></a>
                                </div>
                            </a>
                        </div>
                    </td>
                </tr>

            <?php endwhile ?>

            <tr>
                <td colspan="5">
                    <form class="form-inline" action="" method="post" role="form">
                        <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />
                        <input type="hidden" name="type" value="demo_reports" />

                        <div class="mr-4">
                            <i class="fa fa-plus fa-1x"></i>
                        </div>

                        <div class="form-group mr-4">
                            <input type="text" name="username" class="form-control" placeholder="<?= $language->admin_extra_settings->input->username ?>" value="" required="required" />
                        </div>

                        <div class="form-group mr-4">
                            <select class="custom-select" name="source">
                                <option value="instagram">Instagram</option>

                                <?php

                                foreach($plugins->plugins as $plugin_identifier => $value) {
                                    if($plugins->exists_and_active($plugin_identifier)) {
                                        echo '<option value="' . $plugin_identifier . '">' . ucfirst($plugin_identifier) . '</option>';
                                    }
                                }

                                ?>

                            </select>
                        </div>

                        <div class="form-group mr-4">
                            <select class="custom-select" name="is_featured">
                                <option value="1"><?= sprintf($language->admin_extra_settings->input->is_featured, $language->global->yes) ?></option>
                                <option value="0"><?= sprintf($language->admin_extra_settings->input->is_featured, $language->global->no) ?></option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" name="submit" class="btn btn-primary"><?= $language->global->submit_button ?></button>
                        </div>
                    </form>
                </td>
            </tr>

            </tbody>
        </table>

    </div>
</div>