<?php
defined('MAHIDCODE') || die();

$controller_has_container = false;

/* Include the aos library */
add_event('head', function() {
    echo '<link href="assets/css/aos.min.css" rel="stylesheet" media="screen">';
});


add_event('footer', function() {
    echo '<script src="assets/js/aos.min.js"></script>';

    echo <<<ALTUM
<script>
    $(document).ready(() => {
        AOS.init({
            delay: 50,
            duration: 600
        });
    });
</script>
ALTUM;

});
