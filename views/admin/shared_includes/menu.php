<?php defined('ALTUMCODE') || die() ?>

<nav class="navbar navbar-dark navbar-admin-menu navbar-expand-lg">
	<div class="container">
        <a class="navbar-brand navbar-admin-brand" href="admin/" data-toggle="tooltip" data-title="<?= $language->admin_index->menu ?>">
            <i class="fa fa-globe"></i>
        </a>

        <button class="navbar-toggler navbar-toggler-admin" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

		<div class="collapse navbar-collapse justify-content-end" id="navbar">
			<ul class="navbar-nav navbar-admin-nav">

                <li class="nav-item">
                    <a class="nav-link nav-link-admin" target="_blank" href="<?= $settings->url ?>" data-toggle="tooltip" title="<?= $language->global->menu->website ?>">
                        <i class="fa fa-home"></i> <span class="d-inline d-lg-none"><?= $language->global->menu->website ?></span>
                    </a>
                </li>

                <?php if($plugins->exists_and_active('instagram')): ?>
                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/source-users-management/instagram" data-toggle="tooltip" title="<?= $language->instagram->admin_users_management->menu ?>">
                        <i class="fab fa-instagram"></i> <span class="d-inline d-lg-none"><?= $language->instagram->admin_users_management->menu ?></span>
                    </a>
                </li>
                <?php endif ?>

                <?php if($plugins->exists_and_active('facebook')): ?>
                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/source-users-management/facebook" data-toggle="tooltip" title="<?= $language->facebook->admin_users_management->menu ?>">
                        <i class="fab fa-facebook"></i> <span class="d-inline d-lg-none"><?= $language->facebook->admin_users_management->menu ?></span>
                    </a>
                </li>
                <?php endif ?>

                <?php if($plugins->exists_and_active('youtube')): ?>
                    <li class="nav-item">
                        <a class="nav-link nav-link-admin" href="admin/source-users-management/youtube" data-toggle="tooltip" title="<?= $language->youtube->admin_users_management->menu ?>">
                            <i class="fab fa-youtube"></i> <span class="d-inline d-lg-none"><?= $language->youtube->admi_users_management->menu ?></span>
                        </a>
                    </li>
                <?php endif ?>

                <?php if($plugins->exists_and_active('twitter')): ?>
                    <li class="nav-item">
                        <a class="nav-link nav-link-admin" href="admin/source-users-management/twitter" data-toggle="tooltip" title="<?= $language->twitter->admin_users_management->menu ?>">
                            <i class="fab fa-twitter"></i> <span class="d-inline d-lg-none"><?= $language->twitter->admin_users_management->menu ?></span>
                        </a>
                    </li>
                <?php endif ?>

                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/users-management" data-toggle="tooltip" title="<?= $language->admin_users_management->menu ?>">
                        <i class="fa fa-users"></i> <span class="d-inline d-lg-none"><?= $language->admin_users_management->menu ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/proxies-management" data-toggle="tooltip" title="<?= $language->admin_proxies_management->menu ?>">
                        <i class="fa fa-plug"></i> <span class="d-inline d-lg-none"><?= $language->admin_proxies_management->menu ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/pages-management" data-toggle="tooltip" title="<?= $language->admin_pages_management->menu ?>">
                        <i class="fa fa-file-alt"></i> <span class="d-inline d-lg-none" data-toggle="tooltip" title=""><?= $language->admin_pages_management->menu ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/payments-list" data-toggle="tooltip" title="<?= $language->admin_payments_list->menu ?>">
                        <i class="fa fa-dollar-sign"></i> <span class="d-inline d-lg-none"><?= $language->admin_payments_list->menu ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="admin/website-statistics" data-toggle="tooltip" title="<?= $language->admin_website_statistics->menu ?>">
                        <i class="fa fa-chart-line"></i> <span class="d-inline d-lg-none"><?= $language->admin_website_statistics->menu ?></span>
                    </a>
                </li>

                <li class="dropdown">
                    <a class="nav-link nav-link-admin dropdown-toggle" data-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-wrench"></i> <span class="d-inline d-lg-none"><?= $language->global->menu->admin_settings ?></span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="admin/website-settings"> <?= $language->admin_website_settings->menu ?></a>
                        <a class="dropdown-item" href="admin/extra-settings"> <?= $language->admin_extra_settings->menu ?></a>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-link-admin" href="logout" data-toggle="tooltip" title="<?= $language->global->menu->logout ?>">
                        <i class="fa fa-sign-out-alt"></i> <span class="d-inline d-lg-none"><?= $language->global->menu->logout ?></span>
                    </a>
                </li>

            </ul>
		</div>
	</div>
</nav>
<div class="navbar-admin-menu-border mb-5"></div>
