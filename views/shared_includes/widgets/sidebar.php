<?php defined('ALTUMCODE') || die() ?>

<div class="card card-shadow">
    <div class="card-body">
        <h4 class="card-title d-flex justify-content-between">
            <?= sprintf($language->dashboard->sidebar->header, $account->username) ?>
        </h4>
        <p class="card-text text-muted"><?= $language->dashboard->sidebar->text ?></p>

    </div>
    <div class="list-group">
        <a href="store" class="list-group-item list-group-item-action border-0 <?= $controller == 'store' ? 'active' : null ?>"><i class="fa fa-credit-card"></i> <?= $language->store->menu ?></a>

        <?php if($settings->store_unlock_report_price != '0'): ?>
            <a href="my-reports" class="list-group-item list-group-item-action border-0"><i class="fa fa-copy"></i> <?= $language->my_reports->menu ?></a>

            <?php if($controller == 'my_reports'): ?>
                <?php foreach($plugins->plugins as $plugin_identifier => $value): ?>
                    <?php if($plugins->exists_and_active($plugin_identifier)):?>
                        <a href="my-reports/<?= $plugin_identifier ?>" class="list-group-item list-group-item-action border-0 <?= $source == $plugin_identifier ? 'active' : null ?>"><i class="fa fa-caret-right mr-3"></i> <i class="<?= $language->{$plugin_identifier}->global->icon ?>"></i> <?= $language->{$plugin_identifier}->global->name ?></a>
                    <?php endif ?>
                <?php endforeach ?>
            <?php endif ?>
        <?php endif ?>

        <a href="favorites" class="list-group-item list-group-item-action border-0"><i class="fa fa-heart"></i> <?= $language->favorites->menu ?></a>
        <?php if($controller == 'favorites'): ?>
            <?php foreach($plugins->plugins as $plugin_identifier => $value): ?>
                <?php if($plugins->exists_and_active($plugin_identifier)):?>
                    <a href="favorites/<?= $plugin_identifier ?>" class="list-group-item list-group-item-action border-0 <?= $source == $plugin_identifier ? 'active' : null ?>"><i class="fa fa-caret-right mr-3"></i> <i class="<?= $language->{$plugin_identifier}->global->icon ?>"></i> <?= $language->{$plugin_identifier}->global->name ?></a>
                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>

        <a href="account-settings" class="list-group-item list-group-item-action border-0 <?= $controller == 'account_settings' ? 'active' : null ?>"><i class="fa fa-wrench"></i> <?= $language->account_settings->menu ?></a>
        <a href="api-documentation" class="list-group-item list-group-item-action border-0 <?= $controller == 'api_documentation' ? 'active' : null ?>"><i class="fab fa-keycdn"></i> <?= $language->api_documentation->menu ?></a>
        <a href="logout" class="list-group-item list-group-item-action border-0"><i class="fa fa-sign-out-alt"></i> <?= $language->global->menu->logout ?></a>
    </div>
</div>

<?php if(!empty($settings->account_sidebar_ad) && ((User::logged_in() && !$account->no_ads) || !User::logged_in())): ?>
    <div class="mt-2 mb-1">
        <?= $settings->account_sidebar_ad ?>
    </div>
<?php endif ?>
