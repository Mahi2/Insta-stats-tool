<?php defined('ALTUMCODE') || die() ?>

<table class="table table-responsive-lg mt-3">
    <thead class="thead-black bg-youtube">
    <tr>
        <th></th>
        <th><?= $language->youtube->report->display->title ?></th>
        <th><?= $language->youtube->report->display->subscribers ?></th>
        <th><?= $language->youtube->report->display->views ?></th>
        <th><?= $language->youtube->report->display->videos ?></th>
        <th><?= $language->my_reports->table->expiration_date ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($source_users as $source_account): ?>

        <?php

        /* Get the previous log so that we can compare the current with the previous */
        $previous = $database->query("SELECT `subscribers`, `views`, `videos`, `date` FROM `youtube_logs` WHERE `youtube_user_id` = {$source_account->source_user_id} ORDER BY `date` DESC LIMIT 1, 1")->fetch_object();

        if($previous) {
            $subscribers_diff = $source_account->subscribers - $previous->subscribers;
            $views_diff = $source_account->views - $previous->views;
            $videos_diff = number_format($source_account->videos - $previous->videos, 2, '.', '');
        }

        ?>

        <tr>
            <td><img src="<?= $source_account->profile_picture_url ?>" class="instagram-avatar-small rounded-circle" onerror="$(this).attr('src', ($(this).data('failover')))" data-failover="<?= $settings->url . ASSETS_ROUTE ?>images/default_avatar.png" /></td>
            <td><a href="report/<?= $source_account->youtube_id ?>/youtube"><?= $source_account->title ?></a></td>
            <td>
                <?= nr($source_account->subscribers) ?>

                 <?php if($previous): ?>
                    <?= colorful_number_icon($subscribers_diff, sprintf($language->report->display->comparison, sign_number($subscribers_diff), '')) ?>
                <?php endif ?>
            </td>
            <td>
                <?= nr($source_account->views) ?>

                 <?php if($previous): ?>
                    <?= colorful_number_icon($views_diff, sprintf($language->report->display->comparison, sign_number($views_diff), '')) ?>
                <?php endif ?>
            </td>
            <td>
                <?= nr($source_account->videos) ?>

                <?php if($previous): ?>
                    <?= colorful_number_icon($videos_diff, sprintf($language->report->display->comparison, sign_number($videos_diff), '')) ?>
                <?php endif ?>
            </td>
            <td>
                <?php if($source_account->expiration_date == '0'): ?>
                    <span data-toggle="tooltip" title="<?= $language->my_reports->table->no_expiration_date ?>" class="text-primary"><i class="fas fa-infinity"></i></span>
                <?php else: ?>
                    <span data-toggle="tooltip" title="<?= $source_account->expiration_date ?>"><?= (new DateTime($source_account->expiration_date))->format('Y-m-d') ?></span>
                <?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
