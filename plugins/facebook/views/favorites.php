<?php defined('ALTUMCODE') || die() ?>

<div class="text-white no-underline card bg-facebook bg-facebook-favorites mb-3">
    <div class="card-body d-flex justify-content-between">
        <div>
            <i class="fab fa-facebook"></i> <?= $language->facebook->favorites->display->facebook ?>
        </div>

        <div class=""><?= count($source_users) ?></div>
    </div>
</div>

<table class="table table-responsive-lg mt-3">
    <thead class="thead-black">
    <tr>
        <th><?= $language->facebook->report->display->username ?></th>
        <th><?= $language->facebook->report->display->likes ?></th>
        <th><?= $language->facebook->report->display->followers ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($source_users as $source_account): ?>
        <tr>
            <td><a href="report/<?= $source_account->username ?>/facebook"><?= $source_account->username ?></a></td>
            <td><?= nr($source_account->likes) ?></td>
            <td><?= nr($source_account->followers) ?></td>
            <td>
                <a href="#" id="favorite" onclick="return favorite(event)" data-id="<?= $source_account->id ?>" data-source="facebook" class="text-dark">
                    <?= $language->report->display->remove_favorite ?>
                </a>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
