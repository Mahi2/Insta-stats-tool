<?php defined('ALTUMCODE') || die() ?>

<div class="text-white no-underline card bg-twitter bg-twitter-favorites mb-3">
    <div class="card-body d-flex justify-content-between">
        <div>
            <i class="fab fa-twitter"></i> <?= $language->twitter->favorites->display->twitter ?>
        </div>

        <div class=""><?= count($source_users) ?></div>
    </div>
</div>

<table class="table table-responsive-lg mt-3">
    <thead class="thead-black">
    <tr>
        <th><?= $language->twitter->report->display->username ?></th>
        <th><?= $language->twitter->report->display->followers ?></th>
        <th><?= $language->twitter->report->display->tweets ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($source_users as $source_account): ?>
        <tr>
            <td><a href="report/<?= $source_account->username ?>/twitter"><?= $source_account->username ?></a></td>
            <td><?= nr($source_account->followers) ?></td>
            <td><?= nr($source_account->tweets) ?></td>
            <td>
                <a href="#" id="favorite" onclick="return favorite(event)" data-id="<?= $source_account->id ?>" data-source="twitter" class="text-dark">
                    <?= $language->report->display->remove_favorite ?>
                </a>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
