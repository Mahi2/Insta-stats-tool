<?php defined('ALTUMCODE') || die() ?>

<table class="table table-responsive-lg mt-3">
    <thead class="thead-black bg-twitter">
    <tr>
        <th></th>
        <th><?= $language->twitter->report->display->username ?></th>
        <th><?= $language->twitter->report->display->followers ?></th>
        <th><?= $language->twitter->report->display->following ?></th>
        <th><?= $language->twitter->report->display->tweets ?></th>
        <th><?= $language->twitter->report->display->likes ?></th>
        <th><?= $language->my_reports->table->expiration_date ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($source_users as $source_account): ?>

        <?php

        /* Get the previous log so that we can compare the current with the previous */
        $previous = $database->query("SELECT `followers`, `following`, `tweets`, `likes`, `date` FROM `twitter_logs` WHERE `twitter_user_id` = {$source_account->source_user_id} ORDER BY `date` DESC LIMIT 1, 1")->fetch_object();

        if($previous) {
            $followers_diff = $source_account->followers - $previous->followers;
            $following_diff = $source_account->following - $previous->following;
            $tweets_diff = $source_account->tweets - $previous->tweets;
            $likes_diff = $source_account->likes - $previous->likes;
        }

        ?>

        <tr>
            <td><img src="<?= $source_account->profile_picture_url ?>" class="instagram-avatar-small rounded-circle" onerror="$(this).attr('src', ($(this).data('failover')))" data-failover="<?= $settings->url . ASSETS_ROUTE ?>images/default_avatar.png" /></td>
            <td><a href="report/<?= $source_account->username ?>/twitter"><?= $source_account->username ?></a></td>
            <td>
                <?= nr($source_account->followers) ?>

                 <?php if($previous): ?>
                    <?= colorful_number_icon($followers_diff, sprintf($language->report->display->comparison, sign_number($followers_diff), '')) ?>
                <?php endif ?>
            </td>
            <td>
                <?= nr($source_account->following) ?>

                 <?php if($previous): ?>
                    <?= colorful_number_icon($following_diff, sprintf($language->report->display->comparison, sign_number($following_diff), '')) ?>
                <?php endif ?>

            </td>
            <td>
                <?= nr($source_account->tweets) ?>

                 <?php if($previous): ?>
                    <?= colorful_number_icon($tweets_diff, sprintf($language->report->display->comparison, sign_number($tweets_diff), '')) ?>
                <?php endif ?>
            </td>
            <td>
                <?= nr($source_account->likes) ?>

                 <?php if($previous): ?>
                    <?= colorful_number_icon($likes_diff, sprintf($language->report->display->comparison, sign_number($likes_diff), '')) ?>
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
