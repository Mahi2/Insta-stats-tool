<?php
defined('MAHIDCODE') || die();

$limit = (isset($parameters[0])) ? (int) Database::clean_string($parameters[0]) : false;

/* Set the header as xml so the browser can read it properly */
header('Content-Type: text/xml');

/* Default pagination */
$pagination = 9000;

/* Generate the main sitemap */
if(!is_numeric($limit)) {
    /* Get total number of ig users in this case */
    $total = $database->query("SELECT COUNT(*) AS `total` FROM `instagram_users`")->fetch_object()->total;

    /* Make sure to get the proper total if plugins are active */
    foreach($plugins->plugins as $plugin_identifier => $value) {
        if($plugins->exists_and_active($plugin_identifier)) {

            $column = $plugin_identifier . '_users';

            $total += $database->query("SELECT COUNT(*) AS `total` FROM `{$column}`")->fetch_object()->total;

        }
    }

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    for ($i = 0; $i < $total; $i += $pagination) {

        echo '
        <sitemap>
            <loc>' . $settings->url . 'sitemap/' . $i . '</loc>
            <lastmod>' . (new DateTime())->format('Y-m-d\TH:i:sP') . '</lastmod>
        </sitemap>';

    }

    echo '</sitemapindex>';
}