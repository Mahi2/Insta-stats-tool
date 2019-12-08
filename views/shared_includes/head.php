<?php defined('ALTUMCODE') || die() ?>

<head>
	<title><?= $page_title ?></title>
	<base href="<?= $settings->url ?>">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?php if(!empty($settings->favicon)): ?>
    <link rel="shortcut icon" type="image/png" href="<?= url(UPLOADS_ROUTE . 'favicon/' . $settings->favicon) ?>"/>
    <?php endif ?>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

    <?php
	if(!empty($settings->meta_description) && $controller == 'index')
		echo '<meta name="description" content="' . $settings->meta_description . '" />';

    if(!empty($settings->meta_keywords) && $controller == 'index')
        echo '<meta name="keywords" content="' . $settings->meta_keywords . '" />';
	?>

    <?php foreach(['bootstrap.min.css', 'custom.css', 'fa-svg-with-js.css', 'animate.min.css'] as $file): ?>
	<link href="<?= $settings->url . ASSETS_ROUTE ?>css/<?= $file ?>?v=<?= PRODUCT_CODE ?>" rel="stylesheet" media="screen,print">
    <?php endforeach ?>

    <?php foreach(['jquery-3.2.1.min.js', 'popper.min.js', 'bootstrap.min.js', 'main.js', 'functions.js', 'fontawesome-all.min.js'] as $file): ?>
    <script src="<?= $settings->url . ASSETS_ROUTE ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>"></script>
    <?php endforeach ?>

    <?php perform_event('head') ?>

	<?php if(!empty($settings->analytics_code)): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $settings->analytics_code ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?= $settings->analytics_code ?>');
    </script>
	<?php endif ?>


	<?php if(User::logged_in()): ?>
		<script>
			/* Setting a global csrf token from the login for extra protection */
			csrf_dynamic = '<?= Security::csrf_get_session_token('dynamic') ?>';

			$.ajaxSetup({
				headers: {
					'CSRF-Token-dynamic': csrf_dynamic
				}
			});

		</script>
	<?php endif ?>
</head>
