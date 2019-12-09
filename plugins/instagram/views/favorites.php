<?php defined('ALTUMCODE') || die() ?>

<div class="text-white no-underline card bg-instagram bg-instagram-favorites mb-3">
    <div class="card-body d-flex justify-content-between">
        <div>
            <i class="fab fa-instagram"></i> <?= $language->instagram->favorites->display->instagram ?>
        </div>

        <div class=""><?= count($source_users) ?></div>
    </div>
</div>

<table class="table table-responsive-md">
    <thead class="thead-black">
    <tr>
        <th><?= $language->instagram->report->display->username ?></th>
        <th><?= $language->instagram->report->display->followers ?></th>
        <th><?= $language->instagram->report->display->following ?></th>
        <th><?= $language->instagram->report->display->uploads ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($source_users as $source_account): ?>
        <tr>
            <td><a href="report/<?= $source_account->username ?>/instagram"><?= $source_account->username ?></a></td>
            <td><?= nr($source_account->followers) ?></td>
            <td><?= nr($source_account->following) ?></td>
            <td><?= nr($source_account->uploads) ?></td>
            <td>
                <a href="#" id="favorite" onclick="return favorite(event)" data-id="<?= $source_account->id ?>" data-source="instagram" class="text-dark">
                    <?= $language->report->display->remove_favorite ?>
                </a>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
