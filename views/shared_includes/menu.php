<?php defined('ALTUMCODE') || die() ?>

<nav class="navbar navbar-main navbar-expand-lg navbar-light <?= $controller == 'index' ? 'navbar-main-index' : null ?> <?= !in_array($controller, ['index', 'directory']) ? 'navbar-main-margin' : null ?>">
    <div class="container">
        <a class="navbar-brand" href="<?= $settings->url ?>">
            <?php if($settings->logo != ''): ?>
                <img src="<?= $settings->url . UPLOADS_ROUTE . 'logo/' . $settings->logo ?>" class="img-fluid navbar-logo" alt="<?= $language->global->accessibility->logo_alt ?>" />
            <?php else: ?>
                <?= $settings->title ?>
            <?php endif ?>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_navbar" aria-controls="main_navbar" aria-expanded="false" aria-label="<?= $language->global->accessibility->toggle_navigation ?>">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="main_navbar">
            <ul class="navbar-nav ">

                <?php
                $top_menu_result = $database->query("SELECT `url`, `title` FROM `pages` WHERE `position` = '1'");

                while($top_menu = $top_menu_result->fetch_object()):

                    $link_internal = true;
                    if(strpos($top_menu->url, 'http://') !== false || strpos($top_menu->url, 'https://') !== false) {
                        $link_url = $top_menu->url;
                        $link_internal = false;
                    } else {
                        $link_url = $settings->url . 'page/' . $top_menu->url;
                    }

                    ?>
                    <li class="nav-item"><a class="nav-link" href="<?= $link_url ?>" <?= $link_internal ? null : 'target="_blank"' ?>><?= $top_menu->title ?></a></li>
                <?php endwhile ?>

                <?php if($settings->directory != 'DISABLED'): ?>
                <li class="nav-item"><a class="nav-link" href="directory"> <?= $language->directory->menu ?></a></li>
                <?php endif ?>

                <?php if(!User::logged_in()): ?>

                    <li class="nav-item active"><a class="nav-link" href="login"><i class="fa fa-sm fa-sign-in-alt mr-1"></i> <?= $language->login->menu ?></a></li>
                    <li class="nav-item active"><a class="nav-link" href="register"><i class="fa fa-sm fa-plus mr-1"></i> <?= $language->register->menu ?></a></li>

                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard"> <?= $language->dashboard->menu ?></a></li>

                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                            <img src="<?= get_gravatar($account->email) ?>" class="small-avatar" /> <?= $account->username ?> <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <?php if($account->type > 0): ?>
                                <a class="dropdown-item" href="<?= url('admin') ?>"><i class="fa fa-sm fa-user-shield mr-1"></i> <?= $language->global->menu->admin ?></a>
                            <?php endif ?>
                            <a class="dropdown-item" href="store"><i class="fa fa-sm fa-credit-card mr-1"></i> <?= sprintf($language->store->menu, $account->points) ?></a>
                            <a class="dropdown-item" href="my-reports"><i class="fa fa-sm fa-copy mr-1"></i> <?= $language->my_reports->menu ?></a>
                            <a class="dropdown-item" href="favorites"><i class="fa fa-sm fa-heart mr-1"></i> <?= $language->favorites->menu ?></a>
                            <a class="dropdown-item" href="account-settings"><i class="fa fa-sm fa-wrench mr-1"></i> <?= $language->account_settings->menu ?></a>
                            <a class="dropdown-item" href="api-documentation"><i class="fab fa-keycdn mr-1"></i> <?= $language->api_documentation->menu ?></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout"><i class="fa fa-sm fa-sign-out-alt mr-1"></i> <?= $language->global->menu->logout ?></a>
                        </div>
                    </li>

                <?php endif ?>

            </ul>
        </div>
    </div>
</nav>
