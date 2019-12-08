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

<div class="col-6 col-md-3 mb-3">
        <div class="card card-shadow h-100 zoomer">
            <div class="card-body">
                <div class="card-body pb-0">
                    <p>
                        <span class="card-title h4"><?= $users->active_users_month ?></span>

                        <?= $language->admin_index->display->active_users_month ?>
                    </p>
                </div>

            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card card-shadow h-100 zoomer">
            <div class="card-body pb-0">
                <p>
                    <span class="card-title h4"><?= $transactions_month ?></span>

                    <?= $language->admin_index->display->transactions_month ?>
                </p>
            </div>

            <div class="admin-widget-chart-container">
                <canvas id="transactions"></canvas>
            </div>
        </div>
    </div>
    <script>
        new Chart(document.getElementById('transactions').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= $payments_month_chart['labels'] ?>,
                datasets: [{
                    data: <?= $payments_month_chart['transactions'] ?>,
                    backgroundColor: 'rgba(44, 175, 247, .5)',
                    borderColor: 'rgb(44, 175, 247)',
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
    <div class="col-6 col-md-3 mb-3">
        <div class="card card-shadow h-100 zoomer">
            <div class="card-body pb-0">
                <p>
                    <span class="card-title h4"><span class="text-success"><?= $earnings_month ?></span> <?= $settings->store_currency ?></span>

                    <?= $language->admin_index->display->earnings_month ?>
                </p>
            </div>

            <div class="admin-widget-chart-container">
                <canvas id="earnings"></canvas>
            </div>
        </div>
    </div>
    <script>
        new Chart(document.getElementById('earnings').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= $payments_month_chart['labels'] ?>,
                datasets: [{
                    data: <?= $payments_month_chart['earnings'] ?>,
                    backgroundColor: 'rgba(37, 247, 177, .5)',
                    borderColor: 'rgb(37, 247, 177)',
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
    <div class="col-6 col-md-3 mb-3 mb-md-0 zoomer">
        <div class="card card-shadow h-100">
            <div class="card-body">
                <h4 class="card-title"><?= $reports->unlocked_reports ?></h4>

                <?= $language->admin_index->display->unlocked_reports ?>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3 mb-3 mb-md-0 zoomer">
        <div class="card card-shadow h-100">
            <div class="card-body">
                <h4 class="card-title"><?= $users->active_users ?></h4>

                <?= $language->admin_index->display->active_users ?>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3 mb-md-0 zoomer">
        <div class="card card-shadow h-100">
            <div class="card-body">
                <h4 class="card-title"><?= $payments->transactions ?></h4>

                <?= $language->admin_index->display->transactions ?>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3 mb-3 mb-md-0 zoomer">
        <div class="card card-shadow h-100">
            <div class="card-body">
                <h4 class="card-title"><span class="text-success"><?= $payments->earnings ?></span> <?= $settings->store_currency ?></h4>

                <?= $language->admin_index->display->earnings ?>
            </div>
        </div>
    </div>
</div>
<div class="mb-3 row">
    <div class="col-md-6 mb-3 mb-md-0">
        <div class="card card-shadow h-100">
            <div class="card-body">

                <table class="table table-borderless">
                    <tbody>
                    <tr>
                        <th>⚡️ Version</th>
                        <td><?= PRODUCT_VERSION ?></td>
                    </tr>
                    <th>📚 Documentation</th>
                    <td><a href="<?= PRODUCT_DOCUMENTATION_URL ?>" target="_blank">Check Documentation</a></td>
                    </tr>
                    <tr>
                        <th>👁 Check for updates</th>
                        <td><a href="<?= PRODUCT_URL ?>" target="_blank">Codecanyon</a></td>
                    </tr>
                    <tr>
                        <th>💼 More work of mine</th>
                        <td><a href="https://codecanyon.net/user/altumcode/portfolio" target="_blank">Envato // Codecanyon</a></td>
                    </tr>
                    <tr>
                        <th>🔥 Official website</th>
                        <td><a href="https://altumcode.io/" target="_blank">AltumCode.io</a></td>
                    </tr>
                    <tr>
                        <th>🐦 Twitter Updates <br /><small>No support on twitter</small></th>
                        <td><a href="https://twitter.com/altumcode" target="_blank">@altumcode</a></td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>