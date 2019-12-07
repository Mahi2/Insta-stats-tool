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