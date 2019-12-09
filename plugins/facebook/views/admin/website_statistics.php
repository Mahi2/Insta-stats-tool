<?php defined('ALTUMCODE') || die() ?>

<?php
$logs = [];
$data = $database->query("
SELECT formatted_date, SUM(facebook_users_count) AS `facebook_users_count`, SUM(facebook_logs_count) AS `facebook_logs_count`
FROM (
  SELECT DATE_FORMAT(`last_check_date`, '%Y-%m-%d') AS `formatted_date`, COUNT(*) `facebook_users_count`, 0 `facebook_logs_count`
  FROM `facebook_users`
  WHERE `last_check_date` BETWEEN '{$date_start}' AND DATE_ADD('{$date_end}', INTERVAL 1 DAY)
  GROUP BY `formatted_date`

  UNION ALL

  SELECT DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`, 0 `facebook_users_count`, COUNT(*) `facebook_logs_count`
  FROM `facebook_logs`
  WHERE `date` BETWEEN '{$date_start}' AND DATE_ADD('{$date_end}', INTERVAL 1 DAY)
  GROUP BY `formatted_date`
) as A
GROUP BY formatted_date;
");
while($log = $data->fetch_assoc()) { $logs[] = $log; }

$chart_labels_array = [];
$chart_users_array = $chart_logs_array = [];

for($i = 0; $i < count($logs); $i++) {
    $chart_labels_array[] = (new \DateTime($logs[$i]['formatted_date']))->format($language->global->date->datetime_format);
    $chart_users_array[] = $logs[$i]['facebook_users_count'];
    $chart_logs_array[] = $logs[$i]['facebook_logs_count'];
}

if($language->direction == 'rtl') {
    $chart_labels_array = array_reverse($chart_labels_array);
    $chart_users_array = array_reverse($chart_users_array);
    $chart_logs_array = array_reverse($chart_logs_array);
}

/* Defining the chart data */
$chart_labels = '["' . implode('", "', $chart_labels_array) . '"]';
$chart_users = '[' . implode(', ', $chart_users_array) . ']';
$chart_logs = '[' . implode(', ', $chart_logs_array) . ']';

?>

<div class="card card-shadow mb-5">
    <div class="card-body">
        <h4 class="card-title"><?= $language->facebook->admin_website_statistics->header ?></h4>

        <div class="chart-container">
            <canvas id="checked_facebook_accounts"></canvas>
        </div>

    </div>
</div>

<script>
    /* Display chart */
    new Chart(document.getElementById('checked_facebook_accounts').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $chart_labels ?>,
            datasets: [{
                label: <?= json_encode($language->facebook->admin_website_statistics->checked_users) ?>,
                data: <?= $chart_users ?>,
                backgroundColor: '#F75581',
                borderColor: '#F75581',
                fill: false
            },
            {
                label: <?= json_encode($language->facebook->admin_website_statistics->updated_logs) ?>,
                data: <?= $chart_logs ?>,
                backgroundColor: '#2caff7',
                borderColor: '#2caff7',
                fill: false
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false
            },
            title: {
                text: '',
                display: true
            },
            scales: {
                yAxes: [{
                    ticks: {
                        min: 0
                    }
                }]
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
