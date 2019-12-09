<?php defined('ALTUMCODE') || die() ?>

<div class="card card-shadow">
    <div class="card-body">

    <h3 class="d-flex justify-content-between">
        <?= $custom_page->title ?>

        <?php if(User::logged_in() && $account->type > 0): ?>
        <small>
            <a href="admin/page-edit/<?= $custom_page->page_id ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> <?= $language->page->display->edit ?></a>
        </small>
        <?php endif ?>
    </h3>

    <?= $custom_page->description ?>

    </div>
</div>