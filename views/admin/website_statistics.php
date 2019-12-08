<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-5">
   <div>
       <h3><?= sprintf($language->admin_website_statistics->header, $date_start, $date_end) ?></h3>
       <p class="text-muted"><?= $language->admin_website_statistics->subheader ?></p>
   </div>

    <div>
        <form class="form-inline" id="datepicker_form">
            <input type="hidden" id="base_url" value="<?= $settings->url . 'admin/website-statistics' ?>" />

            <div class="input-group input-group-datepicker">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                </div>

                <input
                    type="text"
                    class="form-control"
                    id="datepicker_input"
                    data-range="true"
                    data-date-format="<?= str_replace(['Y', 'm', 'd', 'F'], ['yyyy', 'mm', 'dd', 'MM'], 'Y-m-d') ?>"
                    data-max="<?= (new DateTime($date))->format('Y-m-d') ?>"
                    name="date_range"
                    value="<?= ($date_string) ? $date_string : '' ?>"
                    placeholder="<?= $language->global->date_range_selector ?>"
                    autocomplete="off"
                >
            </div>

            <button type="submit" class="btn btn-default"><?= $language->global->date_range_selector ?></button>
        </form>
    </div>
</div>

<script>
    /* Datepicker */
    $('#datepicker_input').datepicker({
        language: 'en',
        autoClose: true,
        timepicker: false,
        toggleSelected: false,
        minDate: false,
        maxDate: new Date($('#datepicker_input').data('max')),
    });


    $('#datepicker_form').on('submit', (event) => {
        let date = $("#datepicker_input").val();

        let [ date_start, date_end ] = date.split(',');

        if(typeof date_end == 'undefined') {
            date_end = date_start
        }

        let base_url = $("#base_url").val();

        /* Redirect */
        window.location.href = `${base_url}/${date_start}/${date_end}`;

        event.preventDefault();
    });
</script>

<?php $sales_data = $database->query("SELECT SUM(`amount`) AS `earnings`, `currency`, COUNT(`id`) AS `count` FROM `payments` WHERE `date` BETWEEN '{$date_start}' AND DATE_ADD('{$date_end}', INTERVAL 1 DAY) GROUP BY `currency` ") ?>

<div class="card card-shadow mb-5">
    <div class="card-body">
        <h4 class="card-title"><i class="fa fa-dollar-sign"></i> <?= $language->admin_website_statistics->sales->header ?></h4>

        <?php if(!$sales_data->num_rows): ?>
            <?= $language->admin_website_statistics->sales->no_sales ?>
        <?php else: ?>

        <?php
        $logs = [];
        $data = $database->query("SELECT COUNT(*) AS `total_sales`, DATE_FORMAT(`date`, '%Y-%m-%d') AS `formatted_date`, TRUNCATE(SUM(`amount`), 2) AS `total_earned` FROM `payments` WHERE `date` BETWEEN '{$date_start}' AND DATE_ADD('{$date_end}', INTERVAL 1 DAY) GROUP BY `formatted_date`");
        while($log = $data->fetch_assoc()) { $logs[] = $log; }

        $chart_labels_array = [];
        $chart_total_earned_array = $chart_total_sales_array = [];

        for($i = 0; $i < count($logs); $i++) {
            $chart_labels_array[] = (new \DateTime($logs[$i]['formatted_date']))->format($language->global->date->datetime_format);
            $chart_total_earned_array[] = $logs[$i]['total_earned'];
            $chart_total_sales_array[] = $logs[$i]['total_sales'];
        }

        if($language->direction == 'rtl') {
            $chart_labels_array = array_reverse($chart_labels_array);
            $chart_total_earned_array = array_reverse($chart_total_earned_array);
            $chart_total_sales_array = array_reverse($chart_total_sales_array);
        }

        /* Defining the chart data */
        $chart_labels = '["' . implode('", "', $chart_labels_array) . '"]';
        $chart_total_earned = '[' . implode(', ', $chart_total_earned_array) . ']';
        $chart_total_sales = '[' . implode(', ', $chart_total_sales_array) . ']';

        ?>

        <?php while($sales = $sales_data->fetch_object()): ?>
            <h6 class="text-muted">
                <?= sprintf($language->admin_website_statistics->sales->sales, '<span class="text-info">' . $sales->count . '</span>', '<span class="text-success">' . nr($sales->earnings, 2) . '</span>', $sales->currency) ?>
            </h6>
        <?php endwhile ?>

            <div class="chart-container">
                <canvas id="days_sales"></canvas>
            </div>

            <script>
                /* Display chart */
                new Chart(document.getElementById('days_sales').getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: <?= $chart_labels ?>,
                        datasets: [{
                            label: 'Total Sales',
                            data: <?= $chart_total_sales ?>,
                            backgroundColor: '#237f52',
                            borderColor: '#237f52',
                            fill: false
                        },
                        {
                            label: 'Total Earned',
                            data: <?= $chart_total_earned ?>,
                            backgroundColor: '#37D28D',
                            borderColor: '#37D28D',
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

        <?php endif ?>
    </div>
</div>

<?php
foreach($plugins->plugins as $plugin_identifier => $value) {
    if($plugins->exists_and_active($plugin_identifier)) {
        require_once $plugins->require($plugin_identifier, 'views/admin/website_statistics');
    }
}
?>

<?php
$data = $database->query("
SELECT
    (SELECT COUNT(*) FROM `users` WHERE `date` BETWEEN '{$date_start}' AND DATE_ADD('{$date_end}', INTERVAL 1 DAY)) AS `users_count`,
    (SELECT COUNT(*) FROM `payments` WHERE `date` BETWEEN '{$date_start}' AND DATE_ADD('{$date_end}', INTERVAL 1 DAY)) AS `payments_count`,
    (SELECT COUNT(*) FROM `favorites` WHERE `date` BETWEEN '{$date_start}' AND DATE_ADD('{$date_end}', INTERVAL 1 DAY)) AS `favorites_count`,
    (SELECT COUNT(*) FROM `unlocked_reports` WHERE `date` BETWEEN '{$date_start}' AND DATE_ADD('{$date_end}', INTERVAL 1 DAY)) AS `unlocked_reports_count`
")->fetch_object();
?>

<div class="card card-shadow mb-5">
    <div class="card-body">

        <div class="chart-container">
            <canvas id="new_chart"></canvas>
        </div>

    </div>
</div>


<script>
    /* Display chart */
    new Chart(document.getElementById('new_chart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: [
                <?= json_encode($language->admin_website_statistics->new->users) ?>,
                <?= json_encode($language->admin_website_statistics->new->payments) ?>,
                <?= json_encode($language->admin_website_statistics->new->favorites) ?>,
                <?= json_encode($language->admin_website_statistics->new->unlocked_reports) ?>,
            ],
            datasets: [{
                label: false,
                data: [<?= $data->users_count ?>, <?= $data->payments_count ?>, <?= $data->favorites_count ?>, <?= $data->unlocked_reports_count ?>],
                backgroundColor: ['#007bff', '#37d28d', '#f75581', '#2caff7', '#9684f7', '#cc6af7'],
                borderWidth: 1
            }]
        },
        options: {
            legend: {
                display: false
            },
            title: {
                display: false
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
