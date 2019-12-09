<?php defined('ALTUMCODE') || die() ?>

<div class="row">
    <div class="col-md-8 mb-3 mb-md-0">

        <div class="card card-shadow">
            <div class="card-body">
                <h4 class="card-title"><?= sprintf($language->dashboard->display->header, $settings->title) ?></h4>

                <ul class="list-unstyled">
                    <li><i class="far fa-calendar mr-3"></i> <?= sprintf($language->dashboard->display->joined, (new DateTime($account->date))->format($language->global->date->datetime_format)) ?></li>
                    <li><i class="far fa-credit-card mr-3"></i> <?= sprintf($language->dashboard->display->store, '<strong>' . $account->points . '</strong>') ?></li>
                    <li><i class="fa fa-heart mr-3"></i> <?= sprintf($language->dashboard->display->favorites, '<strong>' . $favorites_count . '</strong>') ?></li>
                    <li><i class="fa fa-copy mr-3"></i> <?= sprintf($language->dashboard->display->reports, '<strong>' . $reports_count . '</strong>') ?></li>
                </ul>
            </div>
        </div>

        <div class="my-3"></div>

        <div>
            <?php require VIEWS_ROUTE . 'shared_includes/widgets/search_container.php' ?>
        </div>

        <div class="my-3"></div>



    </div>

    <div class="col-md-4 my-3 my-md-0">
        <?php include VIEWS_ROUTE . 'shared_includes/widgets/sidebar.php' ?>
    </div>
</div>