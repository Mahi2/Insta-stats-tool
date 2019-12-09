<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <h1 class="d-flex justify-content-between m-0">
        <?= $language->not_found->content ?>
        <small><?= User::generate_go_back_button('index') ?></small>
    </h1>

    <div class="mt-5">
        <h2><?= $language->not_found->reports_header ?></h2>
        <span class="text-muted"><?= $language->not_found->reports_subheader ?></span>

        <?php require VIEWS_ROUTE . 'shared_includes/widgets/example_reports.php' ?>
    </div>
</div>

<div class="mt-5">
    <?php require VIEWS_ROUTE . 'shared_includes/widgets/search_container.php' ?>
</div>