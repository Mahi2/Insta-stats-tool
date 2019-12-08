<?php defined('ALTUMCODE') || die() ?>

<!DOCTYPE html>
<html class="<?= $language->direction ?>" dir="<?= $language->direction ?>">
    <?php require VIEWS_ROUTE . $route . 'shared_includes/head.php' ?>

    <body>
    <?php require VIEWS_ROUTE . $route . 'shared_includes/menu.php' ?>


    <div class="container animated fadeIn">
        <?php display_notifications() ?>

        <?php require VIEWS_ROUTE . $route . $controller . '.php' ?>

        <?php require VIEWS_ROUTE . $route . 'shared_includes/footer.php' ?>
    </div>

    <?php perform_event('footer') ?>

    </body>
</html>