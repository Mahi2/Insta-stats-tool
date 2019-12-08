<?php defined('ALTUMCODE') || die() ?>
<head>
	<title><?= $page_title ?></title>
	<base href="<?= $settings->url ?>">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php if(!empty($settings->favicon)): ?>
        <link rel="shortcut icon" type="image/png" href="<?= url(UPLOADS_ROUTE . 'favicon/' . $settings->favicon) ?>"/>
    <?php endif ?>

    <?php foreach(['bootstrap.min.css', 'custom.css', 'fa-svg-with-js.css', 'animate.min.css'] as $file): ?>
    <link href="<?= $settings->url . ASSETS_ROUTE ?>css/<?= $file ?>" rel="stylesheet" media="screen,print">
    <?php endforeach ?>

    <?php foreach(['jquery-3.2.1.min.js', 'popper.min.js', 'bootstrap.min.js', 'main.js', 'functions.js', 'fontawesome-all.min.js'] as $file): ?>
    <script src="<?= $settings->url . ASSETS_ROUTE ?>js/<?= $file ?>"></script>
    <?php endforeach ?>

    <?php perform_event('head') ?>

	<script>
	/* Setting a global csrf token from the login for extra protection */
	csrf_dynamic = '<?= Security::csrf_get_session_token('dynamic') ?>';

	$.ajaxSetup({
		headers: {
			'CSRF-Token-dynamic': csrf_dynamic
		}
	});
	</script>
</head>
