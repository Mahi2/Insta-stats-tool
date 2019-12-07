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