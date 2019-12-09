<?php defined('ALTUMCODE') || die() ?>

<div class="row">
    <div class="col-md-8 mb-3 mb-md-0">

        <?php if(count($source_users)): ?>

            <?php
            if($plugins->exists_and_active($source)) {
                require_once $plugins->require($source, 'views/favorites');
            }
            ?>

        <?php else: ?>

        <div class="card card-shadow">
            <div class="card-body">
                <h2><span class="underline"><?= $language->favorites->header ?></span></h2>

                <div class="d-flex flex-column align-items-center">
                    <img src="assets/images/my_reports.svg" class="img-50-percent mb-4" />

                    <p class="text-muted mb-4"><?= $language->favorites->no_favorites->message ?></p>

                    <a href="<?= url() ?>" class="btn btn-primary"><?= $language->favorites->no_favorites->button ?></a>
                </div>

            </div>
        </div>

        <?php endif ?>


    </div>

    <div class="col-md-4">
        <?php require VIEWS_ROUTE . 'shared_includes/widgets/sidebar.php' ?>
    </div>
</div>