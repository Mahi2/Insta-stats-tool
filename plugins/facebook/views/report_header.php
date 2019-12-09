<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-sm-row flex-wrap margin-bottom-6">
    <div class="col-sm-4 col-md-3 col-lg-2 pl-sm-0 d-flex justify-content-center justify-content-sm-start">
        <img src="<?= $source_account->profile_picture_url ?>" class="img-responsive rounded-circle facebook-avatar" alt="<?= $source_account->name ?>" />
        <span class="fa-stack fa-xs source-badge-position" style="vertical-align: top;">
            <i class="fas fa-circle text-facebook fa-stack-2x"></i>
            <i class="fab fa-facebook fa-stack-1x fa-inverse"></i>
        </span>
    </div>

    <div class="col-sm-8 col-md-9 col-lg-6 pl-sm-0 d-flex justify-content-center justify-content-sm-start">
        <div class="row d-flex flex-column">
            <p class="m-0">
                <a href="<?= 'https://facebook.com/'.$source_account->username ?>" target="_blank" class="text-dark" rel="nofollow"><?= $source_account->username ?></a>
            </p>

            <div class="d-flex flex-row">
                <h1><?= $source_account->name ?></h1>

                <?php if($source_account->is_verified): ?>
                    <span class="align-self-center ml-3" data-toggle="tooltip" title="<?= $language->facebook->report->display->verified ?>"><i class="fa fa-check-circle user-verified-badge"></i></span>
                <?php endif ?>

                <?php if(User::logged_in()): ?>
                    <a href="#" id="favorite" onclick="return favorite(event)" data-id="<?= $source_account->id ?>" data-source="facebook" class="align-self-center ml-3 card-link text-dark favorite-badge">
                        <?= $is_favorited ? $language->report->display->remove_favorite : $language->report->display->add_favorite ?>
                    </a>
                <?php endif ?>
            </div>

            <?php if(!empty($source_account->details->type)): ?>
            <small class="text-muted"><span data-toggle="tooltip" title="<?= $language->facebook->report->display->type ?>"><i class="fa fa-angle-right"></i></span> <?= $source_account->details->type ?></small>
            <?php endif ?>
        </div>
    </div>

    <div class="col-md-12 col-lg-4 d-flex justify-content-around align-items-center mt-4 mt-lg-0 pl-sm-0">
        <div class="col d-flex flex-column justify-content-center">
            <?= $language->facebook->report->display->likes ?>
            <p class="report-header-number"><?= nr($source_account->likes) ?></p>
        </div>

        <?php if($source_account->followers > 0): ?>
        <div class="col d-flex flex-column justify-content-center">
            <?= $language->facebook->report->display->followers ?>
            <p class="report-header-number"><?= nr($source_account->followers) ?></p>
        </div>
        <?php endif ?>
    </div>
</div>
