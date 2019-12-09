<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-sm-row flex-wrap margin-bottom-6">
    <div class="col-sm-4 col-md-3 col-lg-2 pl-sm-0 d-flex justify-content-center justify-content-sm-start">
        <img src="<?= $source_account->profile_picture_url ?>" class="img-responsive rounded-circle twitter-avatar" alt="<?= $source_account->full_name ?>" />
        <span class="fa-stack fa-xs source-badge-position" style="vertical-align: top;">
            <i class="fas fa-circle text-twitter fa-stack-2x"></i>
            <i class="fab fa-twitter fa-stack-1x fa-inverse"></i>
        </span>
    </div>

    <div class="col-sm-8 col-md-9 col-lg-5 pl-sm-0 d-flex justify-content-center justify-content-sm-start">
        <div class="row d-flex flex-column">
            <p class="m-0">
                <a href="<?= 'https://twitter.com/' . $source_account->username ?>" target="_blank" class="text-dark" rel="nofollow"><?= $source_account->username ?></a>
            </p>

            <div class="d-flex flex-row">
                <h1><?= $source_account->full_name ?></h1>

                <?php if($source_account->is_private): ?>
                    <span class="align-self-center ml-3" data-toggle="tooltip" title="<?= $language->report->display->private ?>"><i class="fa fa-lock user-private-badge"></i></span>
                <?php endif ?>

                <?php if($source_account->is_verified): ?>
                    <span class="align-self-center ml-3" data-toggle="tooltip" title="<?= $language->twitter->report->display->verified ?>"><i class="fa fa-check-circle user-verified-badge"></i></span>
                <?php endif ?>

                <?php if(User::logged_in()): ?>
                    <a href="#" id="favorite" onclick="return favorite(event)" data-id="<?= $source_account->id ?>" data-source="twitter" class="align-self-center ml-3 card-link text-dark favorite-badge">
                        <?= $is_favorited ? $language->report->display->remove_favorite : $language->report->display->add_favorite ?>
                    </a>
                <?php endif ?>
            </div>

            <small class="text-muted"><?= $source_account->description ?></small>
        </div>
    </div>

    <div class="col-md-12 col-lg-5 d-flex justify-content-around align-items-center mt-4 mt-lg-0 pl-sm-0">
        <div class="col d-flex flex-column justify-content-center">
            <?= $language->twitter->report->display->followers ?>
            <p class="report-header-number"><?= nr($source_account->followers) ?></p>
        </div>

        <div class="col d-flex flex-column justify-content-center">
            <?= $language->twitter->report->display->following ?>
            <p class="report-header-number"><?= nr($source_account->following) ?></p>
        </div>

        <div class="col d-flex flex-column justify-content-center">
            <?= $language->twitter->report->display->tweets ?>
            <p class="report-header-number"><?= nr($source_account->tweets) ?></p>
        </div>

        <div class="col d-flex flex-column justify-content-center">
            <?= $language->twitter->report->display->likes ?>
            <p class="report-header-number"><?= nr($source_account->likes) ?></p>
        </div>
    </div>
</div>
