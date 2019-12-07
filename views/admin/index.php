<?php defined('ALTUMCODE') || die() ?>

<?php if(!function_exists('curl_version')): ?>
    <div class="alert alert-danger" role="alert">
        <i class="fa fa-minus"></i> Your web server does not have cURL installed and enabled. Please contact your webhost provider or install cURL.
    </div>
<?php endif ?>