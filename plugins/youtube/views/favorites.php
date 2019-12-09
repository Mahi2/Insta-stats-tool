<?php defined('ALTUMCODE') || die() ?>

<div class="text-white no-underline card bg-youtube bg-youtube-favorites mb-3">
    <div class="card-body d-flex justify-content-between">
        <div>
            <i class="fab fa-youtube"></i> <?= $language->youtube->favorites->display->youtube ?>
        </div>

        <div class=""><?= count($source_users) ?></div>
    </div>
</div>

<table class="table table-responsive-lg mt-3">
    <thead class="thead-black">
    <tr>
        <th><?= $language->youtube->report->display->title ?></th>
        <th><?= $language->youtube->report->display->subscribers ?></th>
        <th><?= $language->youtube->report->display->views ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($source_users as $source_account): ?>
        <tr>
            <td><a href="report/<?= $source_account->youtube_id ?>/youtube"><?= $source_account->title ?></a></td>
            <td><?= nr($source_account->subscribers) ?></td>
            <td><?= nr($source_account->views) ?></td>
            <td>
                <a href="#" id="favorite" onclick="return favorite(event)" data-id="<?= $source_account->id ?>" data-source="youtube" class="text-dark">
                    <?= $language->report->display->remove_favorite ?>
                </a>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>

