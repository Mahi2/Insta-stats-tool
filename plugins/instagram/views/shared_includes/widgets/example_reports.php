<?php
defined('ALTUMCODE') || die();

$example_reports_result = $database->query("SELECT * FROM `instagram_users` WHERE `is_demo` = 1 AND `is_featured` = 1");
?>

<?php while($source_account = $example_reports_result->fetch_object()): ?>
    <div class="card card-shadow mt-5 mb-1 zoomer">
        <div class="card-body">
            <div class="d-flex flex-column flex-sm-row flex-wrap">

                <div class="col-sm-4 col-md-3 col-lg-2 d-flex justify-content-center justify-content-sm-start">
                    <?php if(!empty($source_account->profile_picture_url)): ?>
                        <img src="<?= $source_account->profile_picture_url ?>" onerror="$(this).attr('src', ($(this).data('failover')))" data-failover="<?= $settings->url . ASSETS_ROUTE ?>images/default_avatar.png" class="img-fluid rounded-circle instagram-avatar" alt="<?= $source_account->full_name ?>" />
                    <?php endif ?>

                    <span class="fa-stack fa-xs source-badge-position" style="vertical-align: top;">
                        <i class="fas fa-circle text-instagram fa-stack-2x"></i>
                        <i class="fab fa-instagram fa-stack-1x fa-inverse"></i>
                    </span>
                </div>

                <div class="col-sm-8 col-md-9 col-lg-5 d-flex justify-content-center justify-content-sm-start">
                    <div class="row d-flex flex-column">
                        <p class="m-0">
                            <a href="<?= 'https://instagram.com/'.$source_account->username ?>" target="_blank" class="text-dark" rel="nofollow"><?= '@' . $source_account->username ?></a>
                        </p>

                        <h1>
                            <a class="text-dark" href="report/<?= $source_account->username ?>"><?= $source_account->full_name ?></a>

                            <?php if($source_account->is_private): ?>
                                <span data-toggle="tooltip" title="<?= $language->instagram->report->display->private ?>"><i class="fa fa-lock user-private-badge"></i></span>
                            <?php endif ?>

                            <?php if($source_account->is_verified): ?>
                                <span data-toggle="tooltip" title="<?= $language->instagram->report->display->verified ?>"><i class="fa fa-check-circle user-verified-badge"></i></span>
                            <?php endif ?>
                        </h1>

                        <small class="text-muted"><?= $source_account->description ?></small>

                    </div>
                </div>

                <div class="col-md-12 col-lg-5 d-flex justify-content-around align-items-center mt-4 mt-lg-0">
                    <div class="col d-flex flex-column justify-content-center">
                        <?= $language->instagram->report->display->followers ?>
                        <p class="report-header-number"><?= nr($source_account->followers, 0, true) ?></p>
                    </div>

                    <div class="col d-flex flex-column justify-content-center">
                        <?= $language->instagram->report->display->uploads ?>
                        <p class="report-header-number"><?= nr($source_account->uploads,0, true) ?></p>
                    </div>

                    <div class="col d-flex flex-column justify-content-center">
                        <?= $language->instagram->report->display->engagement_rate ?>
                        <p class="report-header-number">
                            <?php if($source_account->is_private): ?>
                                N/A
                            <?php else: ?>
                                <?= nr($source_account->average_engagement_rate, 2) ?>%
                            <?php endif ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endwhile ?>
