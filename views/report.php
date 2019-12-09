<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?php display_notifications() ?>

    <?php require_once $plugins->require($source, 'views/report_header') ?>

    <?php if(!empty($settings->report_ad) && ((User::logged_in() && !$account->no_ads) || !User::logged_in())): ?>
        <div class="my-5">
            <?= $settings->report_ad ?>
        </div>
    <?php endif ?>


    <?php if($source_account->is_private): ?>

        <div class="d-flex justify-content-center">
            <div class="card card-shadow animated fadeIn col-xs-12 col-sm-12 col-md-7 col-lg-5">
                <div class="card-body">

                    <h4 class="card-title"><?= $language->report->display->private_account ?></h4>
                    <p class="text-muted"><?= $language->report->display->private_account_help ?></p>


                    <div class="mt-4">
                        <a href="report/<?= $user ?>?refresh=<?= Security::csrf_get_session_token('url_token') ?>" class="btn btn-primary btn-block"><?= $language->report->button->refresh ?></a>
                    </div>

                </div>
            </div>
        </div>

    <?php
    elseif(
        (!User::logged_in() || !$has_valid_report)
        && (!User::logged_in() || (User::logged_in() && $account->type != '1'))
        && $settings->store_unlock_report_price != '0'
        && !$source_account->is_demo ):
        ?>
        <div class="d-flex justify-content-center">
            <div class="card card-shadow animated fadeIn col-xs-12 col-sm-12 col-md-7 col-lg-5">
                <div class="card-body">

                    <h4 class="card-title"><?= $language->report->display->unlock ?></h4>
                    <p class="text-muted"><?= sprintf($language->report->display->unlock_helper, $user) ?></p>
                    <p><small class="text-muted"><?= sprintf($language->report->display->unlock_helper2, $settings->store_unlock_report_price, $settings->store_currency) ?></small></p>
                    <?php if($settings->store_unlock_report_time != 0): ?>
                        <p><small class="text-muted"><?= sprintf($language->report->display->unlock_helper3, $settings->store_unlock_report_time) ?></small></p>
                    <?php endif ?>

                    <div class="row mt-4">
                        <?php if(!User::logged_in()): ?>
                            <div class="col-sm mt-1">
                                <a href="login?redirect=report/<?= $user ?>/<?= $source ?>" class="btn btn-primary btn-block"><?= $language->report->button->login ?></a>
                            </div>

                            <div class="col-sm mt-1">
                                <a href="register?redirect=report/<?= $user ?>/<?= $source ?>" class="btn btn-primary bg-instagram btn-block"><?= $language->report->button->register ?></a>
                            </div>
                        <?php else: ?>
                            <div class="col-sm mt-1">
                                <a href="store/unlock_report/<?= $source_account->username ?>/<?= Security::csrf_get_session_token('url_token') ?>/<?= $source ?>" data-confirm="<?= $language->store->confirm_unlock_report ?>" class="btn btn-success btn-block"><?= $language->report->button->purchase ?></a>
                            </div>
                        <?php endif ?>
                    </div>

                </div>
            </div>
        </div>
    <?php else: ?>

        <?php require_once $plugins->require($source, 'views/report') ?>

        <div class="container">
            <div class="d-flex flex-column">
                <small class="text-muted"><?= sprintf($language->report->display->last_successful_check_date, $source_account->last_successful_check_date) ?></small>
                <small class="text-muted"><?= sprintf($language->report->display->last_check_date, $source_account->last_check_date) ?></small>
                <small class="text-muted"><?= sprintf($language->report->display->time_zone, $settings->time_zone) ?></small>
            </div>
        </div>

        <?php $language->global->menu->search_title = $language->global->menu->search_title2 ?>
        <div class="search-container-margin d-print-none">
            <?php require VIEWS_ROUTE . 'shared_includes/widgets/search_container.php' ?>
        </div>

    <?php endif ?>
</div>
