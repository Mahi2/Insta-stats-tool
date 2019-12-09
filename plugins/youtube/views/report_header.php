<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-sm-row flex-wrap margin-bottom-6">
    <div class="col-sm-4 col-md-3 col-lg-2 pl-sm-0 d-flex justify-content-center justify-content-sm-start">
        <img src="<?= $source_account->profile_picture_url ?>" class="img-responsive rounded-circle youtube-avatar" alt="<?= $source_account->title ?>" />
        <span class="fa-stack fa-xs source-badge-position" style="vertical-align: top;">
            <i class="fas fa-circle text-youtube fa-stack-2x"></i>
            <i class="fab fa-youtube fa-stack-1x fa-inverse"></i>
        </span>
    </div>

    <div class="col-sm-8 col-md-9 col-lg-5 pl-sm-0 d-flex justify-content-center justify-content-sm-start">
        <div class="row d-flex flex-column">
            <p class="m-0">
                <a href="<?= 'https://youtube.com/channel/'.$source_account->youtube_id ?>" target="_blank" class="text-dark" rel="nofollow"><?= $source_account->username ?></a>
            </p>

            <div class="d-flex flex-row">
                <h1><?= $source_account->title ?></h1>

                <?php if(User::logged_in()): ?>
                    <a href="#" id="favorite" onclick="return favorite(event)" data-id="<?= $source_account->id ?>" data-source="youtube" class="align-self-center ml-3 card-link text-dark favorite-badge">
                        <?= $is_favorited ? $language->report->display->remove_favorite : $language->report->display->add_favorite ?>
                    </a>
                <?php endif ?>
            </div>

            <small class="text-muted"><?= $source_account->description ?></small>

        </div>
    </div>

    <div class="col-md-12 col-lg-5 d-flex justify-content-around align-items-center mt-4 mt-lg-0 pl-sm-0">
        <div class="col d-flex flex-column justify-content-center">
            <?= $language->youtube->report->display->subscribers ?>
            <p class="report-header-number"><?= nr($source_account->subscribers) ?></p>
        </div>

        <div class="col d-flex flex-column justify-content-center">
            <?= $language->youtube->report->display->views ?>
            <p class="report-header-number"><?= nr($source_account->views) ?></p>
        </div>

        <div class="col d-flex flex-column justify-content-center">
            <?= $language->youtube->report->display->videos ?>
            <p class="report-header-number"><?= nr($source_account->videos) ?></p>
        </div>
    </div>
</div>
