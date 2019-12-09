<?php defined('ALTUMCODE') || die() ?>

<!DOCTYPE html>
<html lang="<?= $language->language_code ?>" class="<?= $language->direction ?>" dir="<?= $language->direction ?>">
    <?php require VIEWS_ROUTE . $route . 'shared_includes/head.php' ?>

    <body <?= $controller == 'index' ? 'class="index-body"' : null ?>>
        <?php require VIEWS_ROUTE . $route . 'shared_includes/menu.php' ?>

        <?php if($controller_has_container): ?>
        <main class="container">
            <?php display_notifications() ?>
        <?php endif ?>


        <?php require VIEWS_ROUTE . $route . $controller . '.php' ?>


        <?php if($controller_has_container): ?>
        </main>
        <?php endif ?>

        <?php require VIEWS_ROUTE . $route . 'shared_includes/footer.php' ?>

        <?php perform_event('footer') ?>
    </body>
</html>