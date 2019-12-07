<?php defined('ALTUMCODE') || die() ?>

<?php if(!function_exists('curl_version')): ?>
    <div class="alert alert-danger" role="alert">
        <i class="fa fa-minus"></i> Your web server does not have cURL installed and enabled. Please contact your webhost provider or install cURL.
    </div>
<?php endif ?>

<?php if(!function_exists('iconv')): ?>
    <div class="alert alert-danger" role="alert">
        <i class="fa fa-minus"></i> Your web server disabled the <strong>iconv()</strong> php function. Please contact your webhost provider or install php with iconv().
    </div>
<?php endif ?>

<?php if(version_compare(PHP_VERSION, '7.0.0', '<')): ?>
    <div class="alert alert-danger" role="alert">
        <i class="fa fa-minus"></i> You are on PHP Version <strong><?= PHP_VERSION ?></strong> and the script requires at least <strong>PHP 7 or above</strong>.
    </div>
<?php endif ?>