<?php
defined('ALTUMCODE') || die();
User::check_permission(1);

$payments = $database->query("SELECT COUNT(*) AS `transactions`, IFNULL(TRUNCATE(SUM(`amount`), 2), 0) AS `earnings` FROM `payments` WHERE `currency` = '{$settings->store_currency}'")->fetch_object();
$reports = $database->query("SELECT COUNT(*) AS `unlocked_reports` FROM `unlocked_reports`")->fetch_object();
$users = $database->query("
    SELECT
      (SELECT COUNT(*) FROM `users` WHERE MONTH(`last_activity`) = MONTH(CURRENT_DATE()) AND YEAR(`last_activity`) = YEAR(CURRENT_DATE())) AS `active_users_month`,
      (SELECT COUNT(*) FROM `users`) AS `active_users`
")->fetch_object();

/* Data for the months report of unlocked reports */
$reports_month_result = $database->query("SELECT COUNT(*) AS `unlocked_reports`, DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date` FROM `unlocked_reports` WHERE MONTH(`date`) = MONTH(CURRENT_DATE()) AND YEAR(`date`) = YEAR(CURRENT_DATE()) GROUP BY `formatted_date`");
$reports_month_chart = [
    'labels'        => [],
    'data'          => []
];
$reports_month = 0;

/* Iterating and storing proper data for charts and later use */
while($data = $reports_month_result->fetch_object()) {

    $reports_month_chart['labels'][] = (new \DateTime($data->formatted_date))->format($language->global->date->datetime_format);
    $reports_month_chart['data'][] = $data->unlocked_reports;
    $reports_month += $data->unlocked_reports;
}

/* Defining the chart data */
$reports_month_chart = generate_chart_data($reports_month_chart);

/* Data for the months transactions and earnings */
$payments_month_result = $database->query("SELECT COUNT(*) AS `transactions`, DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`, TRUNCATE(SUM(`amount`), 2) AS `earnings` FROM `payments` WHERE MONTH(`date`) = MONTH(CURRENT_DATE()) AND YEAR(`date`) = YEAR(CURRENT_DATE()) GROUP BY `formatted_date`");
$payments_month_chart = [
    'labels'        => [],
    'transactions'  => [],
    'earnings'      => []
];
$transactions_month = 0;
$earnings_month = 0;

/* Iterating and storing proper data for charts and later use */
while($data = $payments_month_result->fetch_object()) {

    $payments_month_chart['labels'][] = (new \DateTime($data->formatted_date))->format($language->global->date->datetime_format);
    $payments_month_chart['transactions'][] = $data->transactions;
    $payments_month_chart['earnings'][] = $data->earnings;
    $transactions_month += $data->transactions;
    $earnings_month += $data->earnings;

}