<?php
defined('ALTUMCODE') || die();

$example_reports_result = $database->query("SELECT * FROM `youtube_users` WHERE `is_demo` = 1 AND `is_featured` = 1");
?>

<?php while($source_account = $example_reports_result->fetch_object()): ?>
    <div class="card card-shadow mt-5 mb-1 zoomer">
        <div class="card-body">
            <div class="d-flex flex-column flex-sm-row flex-wrap">

                <div class="col-sm-4 col-md-3 col-lg-2 d-flex justify-content-center justify-content-sm-start">
                    <?php if(!empty($source_account->profile_picture_url)): ?>
                        <img src="<?= $source_account->profile_picture_url ?>" onerror="$(this).attr('src', ($(this).data('failover')))" data-failover="<?= $settings->url . ASSETS_ROUTE ?>images/default_avatar.png" class="img-fluid rounded-circle instagram-avatar" alt="<?= $source_account->title ?>" />
                    <?php endif ?>

                    <span class="fa-stack fa-xs source-badge-position" style="vertical-align: top;">
                        <i class="fas fa-circle text-youtube fa-stack-2x"></i>
                        <i class="fab fa-youtube fa-stack-1x fa-inverse"></i>
                    </span>
                </div>

                <div class="col-sm-8 col-md-9 col-lg-5 d-flex justify-content-center justify-content-sm-start">
                    <div class="row d-flex flex-column">
                        <p class="m-0">
                            <a href="<?= 'https://youtube.com/channel/'.$source_account->youtube_id ?>" target="_blank" class="text-dark" rel="nofollow"><?= $source_account->title ?></a>

                            <?php ?>
                        </p>

                        <h1>
                            <a class="text-dark" href="report/<?= $source_account->youtube_id ?>/youtube"><?= $source_account->title ?></a>
                        </h1>

                        <small class="text-muted"><?= $source_account->description ?></small>

                    </div>
                </div>

                <div class="col-md-12 col-lg-5 d-flex justify-content-around align-items-center mt-4 mt-lg-0">
                    <div class="col d-flex flex-column justify-content-center">
                        <?= $language->youtube->report->display->subscribers ?>
                        <p class="report-header-number"><?= nr($source_account->subscribers, 0, true) ?></p>
                    </div>

                    <div class="col d-flex flex-column justify-content-center">
                        <?= $language->youtube->report->display->views ?>
                        <p class="report-header-number"><?= nr($source_account->views, 0, true) ?></p>
                    </div>

                    <div class="col d-flex flex-column justify-content-center">
                        <?= $language->youtube->report->display->videos ?>
                        <p class="report-header-number"><?= nr($source_account->videos, 0, true) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endwhile ?>
