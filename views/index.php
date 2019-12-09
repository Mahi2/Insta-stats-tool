<?php defined('ALTUMCODE') || die() ?>

<div class="index-container">
    <div class="container">

        <div class="row index-row">
            <div class="col-12 col-lg-7">

                <h1 class="index-heading" data-aos="fade-down"><?= $settings->title ?></h1>

                <p class="index-subheading text-muted pt-1" data-aos="fade-down" data-aos-delay="150"><?= $language->index->subheader ?></p>

                <?php display_notifications() ?>

                <div class="index-search" data-aos="fade-down" data-aos-delay="300">
                    <form class="form-inline d-inline-flex search_form" action="" method="GET">

                        <?php if(count($sources) > 1): ?>
                        <div class="dropdown my-2">
                            <button class="btn btn-light index-source-button dropdown-toggle border-0" data-source="<?= reset($sources) ?>" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="<?= $language->{reset($sources)}->global->icon ?>"></i> <?= $language->{reset($sources)}->global->name ?></button>

                            <div class="dropdown-menu">
                                <?php foreach($sources as $source): ?>
                                    <a class="dropdown-item source-select-item" href="#" data-source="<?= $source ?>"><i class="<?= $language->{$source}->global->icon ?>"></i> <?= $language->{$source}->global->name ?></a>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <?php endif ?>

                        <div class="index-input-div">
                            <i class="fa fa-search index-search-input-icon"></i>
                            <input class="form-control index-search-input border-0 form-control-lg source_search_input" type="text" placeholder="<?= $language->{reset($sources)}->global->search_placeholder ?>" aria-label="<?= $language->{reset($sources)}->global->search_placeholder ?>">
                        </div>

                        <button type="submit" class="btn btn-<?= reset($sources) ?> index-submit-button border-0 d-inline-block"><?= $language->global->search ?></button>
                    </form>
                </div>

            </div>

            <div class="d-none d-lg-flex col justify-content-center">
                <img src="assets/images/index/illustration.svg" class="img-fluid align-self-end index-landing-image" data-aos="fade" data-aos-delay="150" />
            </div>
        </div>
    </div>

    <?php if(!empty($settings->index_ad) && ((User::logged_in() && !$account->no_ads) || !User::logged_in())): ?>
        <div class="container my-3">
            <?= $settings->index_ad ?>
        </div>
    <?php endif ?>

    </div>
</div>

<div class="animated fadeIn">

    <div class="container index-container-margin-top-big">
        <h2><?= $language->index->reports->header ?></h2>
        <span class="text-muted"><?= $language->index->reports->subheader ?></span>

        <?php require VIEWS_ROUTE . 'shared_includes/widgets/example_reports.php' ?>
    </div>



    <div class="container margin-top-6">
        <h2><?= $language->index->presentation->header ?></h2>
        <span class="text-muted"><?= $language->index->presentation->subheader ?></span>

        <div class="row align-items-center margin-top-6">
            <div class="col-md-6">
                <img src="<?= url(ASSETS_ROUTE . 'images/index/one.jpg') ?>" data-aos="fade" class="lozad img-fluid index-presentation-image" alt="Image containing growth statistics chart" title="Growth Statistics" />
            </div>

            <div class="col-md-6" data-aos="fade-left">

                <h3><?= $language->index->presentation->one->header ?></h3>
                <p class="text-muted"><?= $language->index->presentation->one->subheader ?></p>

            </div>
        </div>

        <div class="row align-items-center margin-top-6">
            <div class="col-md-6" data-aos="fade-right">

                <h3><?= $language->index->presentation->two->header ?></h3>
                <p class="text-muted"><?= $language->index->presentation->two->subheader ?></p>

            </div>

            <div class="col-md-6">
                <img src="<?= url(ASSETS_ROUTE . 'images/index/two.jpg') ?>" data-aos="fade" class="lozad img-fluid index-presentation-image" alt="Image containing account statistics from different time frames" title="Past, present and future growth" />
            </div>
        </div>

        <div class="row align-items-center margin-top-6">
            <div class="col-md-6">
                <img src="<?= url(ASSETS_ROUTE . 'images/index/three.jpg') ?>" data-aos="fade" class="lozad img-fluid index-presentation-image" alt="Image containing a demo of an email report" title="Email Reports" />
            </div>

            <div class="col-md-6" data-aos="fade-left">

                <h3><?= sprintf($language->index->presentation->three->header, ucfirst(strtolower($settings->email_reports_frequency))) ?></h3>
                <p class="text-muted"><?= $language->index->presentation->three->subheader ?></p>

            </div>
        </div>
    </div>


    <div class="container index-container-margin-top-big">
        <div class="row mt-5 d-flex">
            <div class="col-12 col-sm-6 col-md-4 mb-3 mb-md-5 ">
                <div data-aos="fade-down" class="card border-0 index-card h-100 h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fa fa-file-pdf index-big-icon"></i>
                        <h5 class="font-weight-bolder mt-5"><?= $language->index->box->pdf_exports ?></h5>
                        <span class="text-muted mt-1"><?= $language->index->box->pdf_exports_text ?></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-3 mb-md-5">
                <div data-aos="fade-down" class="card border-0 index-card h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fa fa-users index-big-icon"></i>
                        <h5 class="font-weight-bolder mt-5"><?= $language->index->box->comparison_tool ?></h5>
                        <span class="text-muted mt-1"><?= $language->index->box->comparison_tool_text ?></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-3 mb-md-5">
                <div data-aos="fade-down" class="card border-0 index-card h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fa fa-chart-line index-big-icon"></i>
                        <h5 class="font-weight-bolder mt-5"><?= $language->index->box->future_projections ?></h5>
                        <span class="text-muted mt-1"><?= $language->index->box->future_projections_text ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div style="margin: 6rem auto;">
    <?php require VIEWS_ROUTE . 'shared_includes/widgets/search_container.php' ?>
</div>