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

/* Generate the sub sitemap */
else {

    $sql = "SELECT  `username`, `last_check_date`, 'instagram' AS `source` FROM `instagram_users`";

    foreach($plugins->plugins as $plugin_identifier => $value) {
        if($plugins->exists_and_active($plugin_identifier)) {

            $column = $plugin_identifier . '_users';

            $sql .= " UNION SELECT  `username`, `last_check_date`, '{$plugin_identifier}' AS `source` FROM `{$column}`";
        }
    }

    $sql .= "LIMIT {$limit}, {$pagination}";

    $source_users_result = $database->query($sql);

    /* Custom pages */
    if($limit == 0) {
        $pages_result = $database->query("SELECT `url` FROM `pages` WHERE `url` NOT LIKE 'https://%' AND `url` NOT LIKE 'http://%'");
    }
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<url>

    <loc><?= $settings->url ?></loc>

    <changefreq>monthly</changefreq>

    <priority>0.8</priority>

</url>

<?php while($report = $source_users_result->fetch_object()): ?>
<url>

    <loc><?= $settings->url . 'report/' . $report->username . '/' . $report->source ?></loc>

    <lastmod><?= (new DateTime($report->last_check_date))->format('Y-m-d\TH:i:sP') ?></lastmod>

    <changefreq>daily</changefreq>

    <priority>0.9</priority>

</url>
<?php endwhile ?>

<?php if($limit == 0) while($page = $pages_result->fetch_object()): ?>
    <url>
        <loc><?= $settings->url . 'page/' . $page->url ?></loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
<?php endwhile ?>
</urlset>

<?php } ?>

<?php $controller_has_view = false;