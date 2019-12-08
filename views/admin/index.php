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

<div class="mb-3 row justify-content-between">
    <div class="col-6 col-md-3 mb-3">
        <div class="card card-shadow h-100 zoomer">
            <div class="card-body pb-0">
                <p>
                    <span class="card-title h4"><?= $reports_month ?></span>

                    <?= $language->admin_index->display->unlocked_reports_month ?>
                </p>
            </div>

            <div class="admin-widget-chart-container">
                <canvas id="unlocked_reports"></canvas>
            </div>
        </div>
    </div>

    <script>
    new Chart(document.getElementById('unlocked_reports').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= $reports_month_chart['labels'] ?>,
                datasets: [{
                    data: <?= $reports_month_chart['data'] ?>,
                    backgroundColor: 'rgba(237, 73, 86, .5)',
                    borderColor: 'rgb(237, 73, 86)',
                    fill: true
                }]
            },
            options: {
                legend: {
                    display: false
                },
                tooltips: {
                    enabled: false
                },
                title: {
                    display: false
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        display: false,
                        gridLines: false,
                        ticks: {
                            userCallback: (value, index, values) => {
                                if(Math.floor(value) === value) {
                                    return number_format(value, 0, '<?= $language->global->number->decimal_point ?>', '<?= $language->global->number->thousands_separator ?>');
                                }
                            }
                        }
                    }],
                    xAxes: [{
                        display: false,
                        gridLines: false,
                    }]
                }
            }
        });
    </script>