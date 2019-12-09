<?php defined('ALTUMCODE') || die() ?>

<div class="row">
    <div class="col-md-8 mb-3 mb-md-0">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h2><span class="underline"><?= $language->my_reports->header ?></span></h2>

                    <?php if(count($source_users)): ?>
                    <a href="<?= csv_link_exporter($source_users_csv) ?>" download="report.csv" target="_blank" class="align-self-start btn btn-light"><i class="fas fa-file-csv"></i> <?= $language->global->export_csv ?></a>
                    <?php endif ?>
                </div>
                <p><?= sprintf($language->my_reports->subheader, '<i class="' . $language->{$source}->global->icon . ' text-' . $source . '"></i> ' . $language->{$source}->global->name) ?></p>

                <div class="margin-top-3"></div>

                <?php if(count($source_users)): ?>

                    <?php
                    if($plugins->exists_and_active($source)) {
                        require_once $plugins->require($source, 'views/my_reports');
                    }
                    ?>

                <?php else: ?>

                    <div class="d-flex flex-column align-items-center">
                        <img src="assets/images/my_reports.svg" class="img-50-percent mb-4" />

                        <p class="text-muted mb-4"><?= $language->my_reports->no_unlocked_reports->message ?></p>

                        <a href="<?= url() ?>" class="btn btn-primary"><?= $language->my_reports->no_unlocked_reports->button ?></a>
                    </div>

                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <?php require VIEWS_ROUTE . 'shared_includes/widgets/sidebar.php' ?>
    </div>
</div>