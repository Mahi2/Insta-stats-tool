<?php defined('ALTUMCODE') || die() ?>

<h2><?= $language->youtube->compare->header ?></h2>
<p class="text-muted"><?= $language->youtube->compare->header_help ?></p>

<div class="compare-search">
    <form class="form-inline d-inline-flex justify-content-center" action="" method="GET" id="compare_search_form">
        <input class="form-control compare-search-input" type="search" id="user_one" value="<?= $user_one ?>" placeholder="<?= $language->youtube->compare->display->search_input_placeholder ?>" aria-label="<?= $language->youtube->compare->display->search_input_placeholder ?>" required="required">

        <span class="mx-3"><?= $language->youtube->compare->display->compare_text ?></span>

        <input class="form-control compare-search-input" type="search" id="user_two" value="<?= $user_two ?>" placeholder="<?= $language->youtube->compare->display->search_input_placeholder ?>" aria-label="<?= $language->youtube->compare->display->search_input_placeholder ?>" required="required">

        <button type="submit" class="btn btn-light compare-submit-button d-inline-block mx-3"><?= $language->global->search ?></button>
    </form>
</div>


<?php if($user_one && $source_account_one && $user_two && $source_account_two && $access): ?>
    <hr />

    <div class="row mt-5 align-items-center">

        <div class="col">
            <div class="d-flex justify-content-end">

                <div class="d-flex flex-column justify-content-center">
                    <p class="m-0 text-right">
                        <a href="<?= 'https://youtube.com/channel/'.$source_account_one->youtube_id ?>" target="_blank" class="text-dark" rel="nofollow"><?= $source_account_one->youtube_id ?></a>
                    </p>

                    <h3 class="text-right">
                        <?= $source_account_one->title ?>
                    </h3>

                    <div class="d-flex justify-content-end">
                        <a href="report/<?= $user_one ?>/youtube" class="btn btn-default btn-sm"><?= $language->youtube->compare->display->view_report ?></a>
                    </div>
                </div>

                <img src="<?= $source_account_one->profile_picture_url ?>" onerror="$(this).attr('src', ($(this).data('failover')))" data-failover="<?= $settings->url . ASSETS_ROUTE ?>images/default_avatar.png" class="img-responsive rounded-circle instagram-avatar ml-3" alt="<?= $source_account_one->title ?>" />
            </div>
        </div>

        <div class="col-12 col-md-1 d-flex justify-content-center">
            <?= $language->youtube->compare->display->compare_text ?>
        </div>

        <div class="col">
            <div class="d-flex">

                <img src="<?= $source_account_two->profile_picture_url ?>" onerror="$(this).attr('src', ($(this).data('failover')))" data-failover="<?= $settings->url . ASSETS_ROUTE ?>images/default_avatar.png" class="img-responsive rounded-circle instagram-avatar mr-3" alt="<?= $source_account_two->title ?>" />

                <div class="d-flex flex-column justify-content-center">
                    <p class="m-0">
                        <a href="<?= 'https://youtube.com/'.$source_account_two->youtube_id ?>" target="_blank" class="text-dark" rel="nofollow"><?= $source_account_two->youtube_id ?></a>
                    </p>

                    <h3>
                        <?= $source_account_two->title ?>
                    </h3>

                    <div>
                        <a href="report/<?= $user_two ?>/youtube" class="btn btn-default btn-sm"><?= $language->youtube->compare->display->view_report ?></a>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="mt-5">
        <h2><?= $language->youtube->compare->display->statistics ?></h2>

        <table class="table table-responsive-md">
            <thead class="thead-black">
            <tr>
                <th class="th-33"></th>

                <th class="th-33">
                    <?= $source_account_one->title ?>
                </th>

                <th class="th-33">
                    <?= $source_account_two->title ?>
                </th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <th>
                    <?= $language->youtube->compare->display->subscribers ?>
                </th>

                <td class="<?= ($first_success = $source_account_one->subscribers > $source_account_two->subscribers) ? 'table-success' : 'table-danger' ?>">
                    <?= nr($source_account_one->subscribers) ?>
                </td>

                <td class="<?= !$first_success ? 'table-success' : 'table-danger' ?>">
                    <?= nr($source_account_two->subscribers) ?>
                </td>
            </tr>

            <tr>
                <th>
                    <?= $language->youtube->compare->display->views ?>
                </th>

                <td class="<?= ($first_success = $source_account_one->views > $source_account_two->views) ? 'table-success' : 'table-danger' ?>">
                    <?= nr($source_account_one->views) ?>
                </td>

                <td class="<?= !$first_success ? 'table-success' : 'table-danger' ?>">
                    <?= nr($source_account_two->views) ?>
                </td>
            </tr>

            <tr>
                <th>
                    <?= $language->youtube->compare->display->videos ?>
                </th>

                <td class="<?= ($first_success = $source_account_one->videos > $source_account_two->videos) ? 'table-success' : 'table-danger' ?>">
                    <?= nr($source_account_one->videos) ?>
                </td>

                <td class="<?= !$first_success ? 'table-success' : 'table-danger' ?>">
                    <?= nr($source_account_two->videos) ?>
                </td>
            </tr>

            </tbody>
        </table>
    </div>


    <div class="mt-5">
        <h2><?= $language->youtube->compare->display->subscribers_chart ?></h2>

        <div class="chart-container">
            <canvas id="subscribers_chart"></canvas>
        </div>
    </div>

    <div class="mt-5">
        <h2><?= $language->youtube->compare->display->views_chart ?></h2>

        <div class="chart-container">
            <canvas id="views_chart"></canvas>
        </div>
    </div>

    <div class="mt-5">
        <h2><?= $language->youtube->compare->display->videos_chart ?></h2>

        <div class="chart-container">
            <canvas id="videos_chart"></canvas>
        </div>
    </div>

    <script>
        Chart.defaults.global.elements.line.borderWidth = 4;
        Chart.defaults.global.elements.point.radius = 3;
        Chart.defaults.global.elements.point.borderWidth = 7;

        new Chart(document.getElementById('subscribers_chart').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= $chart_labels ?>,
                datasets: [{
                    label: '<?= $source_account_one->title ?>',
                    data: <?= $chart_subscribers_one ?>,
                    backgroundColor: '#ED4956',
                    borderColor: '#ED4956',
                    fill: false
                },
                {
                    label: '<?= $source_account_two->title ?>',
                    data: <?= $chart_subscribers_two ?>,
                    backgroundColor: '#2caff7',
                    borderColor: '#2caff7',
                    fill: false
                }]
            },
            options: {
                spanGaps: true,
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                title: {
                    display: false
                },
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true,
                            userCallback: (value, index, values) => {
                                if(Math.floor(value) === value) {
                                    return number_format(value, 0, '<?= $language->global->number->decimal_point ?>', '<?= $language->global->number->thousands_separator ?>');
                                }
                            }
                        }
                    }],
                    xAxes: [{
                        ticks: {
                        }
                    }]
                }
            }
        });

        new Chart(document.getElementById('views_chart').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= $chart_labels ?>,
                datasets: [{
                    label: '<?= $source_account_one->title ?>',
                    data: <?= $chart_views_one ?>,
                    backgroundColor: '#ED4956',
                    borderColor: '#ED4956',
                    fill: false
                },
                {
                    label: '<?= $source_account_two->title ?>',
                    data: <?= $chart_views_two ?>,
                    backgroundColor: '#2caff7',
                    borderColor: '#2caff7',
                    fill: false
                }]
            },
            options: {
                spanGaps: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                title: {
                    display: false
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        new Chart(document.getElementById('videos_chart').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= $chart_labels ?>,
                datasets: [{
                    label: '<?= $source_account_one->title ?>',
                    data: <?= $chart_videos_one ?>,
                    backgroundColor: '#ED4956',
                    borderColor: '#ED4956',
                    fill: false
                },
                    {
                        label: '<?= $source_account_two->title ?>',
                        data: <?= $chart_videos_two ?>,
                        backgroundColor: '#2caff7',
                        borderColor: '#2caff7',
                        fill: false
                    }]
            },
            options: {
                spanGaps: true,
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                title: {
                    display: false
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

    </script>

<?php endif ?>
