<?php defined('ALTUMCODE') || die() ?>

    <div class="margin-bottom-6">
        <div class="row">
            <div class="col">
                <h5><?= $language->report->display->actions ?></h5>
            </div>

            <div class="col">
                <div class="btn-group" role="group">
                    <?php if($settings->store_unlock_report_price == '0' || $has_valid_report || (User::logged_in() && $account->type == '1')): ?>
                        <a href="api?api_key=<?= $account->api_key ?? 0 ?>&username=<?= $source_account->username ?>&source=<?= $source ?>" class="btn btn-light" target="_blank"><i class="fab fa-keycdn text-muted"></i> <?= $language->report->display->api_link ?></a>
                        <button type="button" onclick="window.print()" class="btn btn-light"><i class="fa fa-file-pdf text-muted"></i> <?= $language->report->display->pdf_link ?></button>
                    <?php endif ?>
                    <a href="compare/<?= $source ?>/<?= $source_account->username ?>" class="btn btn-light"><i class="fa fa-users text-muted"></i> <?= $language->report->display->compare ?></a>
                </div>
            </div>
        </div>

        <?php if($source_account->details->country): ?>
        <div class="row">
            <div class="col-2">
                <h5>
                    <?= $language->youtube->report->display->country ?>
                    <span><i class="fa fa-flag text-muted"></i></span>
                </h5>
            </div>

            <div class="col">
                <span><?= $source_account->details->country ?></span>
            </div>
        </div>
        <?php endif ?>

        <?php if($source_account->details->created_date): ?>
        <div class="row">
            <div class="col-2">
                <h5>
                    <?= $language->youtube->report->display->created_date ?>
                    <span><i class="fa fa-calendar text-muted"></i></span>
                </h5>
            </div>

            <div class="col">
                <span><?= (new DateTime($source_account->details->created_date))->format($language->global->date->datetime_format) ?></span>
            </div>
        </div>
        <?php endif ?>

    </div>

    <?php if(count($logs) == 1): ?>
    <div class="alert alert-info mb-5" role="alert">
        <?= $language->report->info_message->recently_generated ?>
    </div>
    <?php endif ?>

    <div class="margin-bottom-6">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center ">
            <h2><?= $language->report->display->statistics_summary ?></h2>

            <?php if($settings->store_unlock_report_price == '0' || $has_valid_report || $source_account->is_demo): ?>
                <div>
                    <form class="form-inline" id="datepicker_form">
                        <input type="hidden" id="base_url" value="<?= $settings->url . 'report/' . $source_account->username . '/' . $source ?>" />

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
                                data-min="<?= (new DateTime($source_account->added_date))->format('Y-m-d') ?>"
                                name="date_range"
                                value="<?= ($date_string) ? $date_string : '' ?>"
                                placeholder="<?= $language->global->date_range_selector ?>"
                                autocomplete="off"
                            >
                        </div>

                        <button type="submit" class="btn btn-default"><?= $language->global->date_range_selector ?></button>
                    </form>
                </div>
            <?php endif ?>
        </div>


        <div class="chart-container">
            <canvas id="subscribers_chart"></canvas>
        </div>

        <div class="chart-container mt-3">
            <canvas id="views_videos_chart"></canvas>
        </div>
    </div>


    <div class="margin-bottom-6">
        <div class="d-flex justify-content-between">
            <h2><?= $language->report->display->summary ?></h2>

            <a href="<?= csv_link_exporter(csv_exporter($logs, ['id', 'youtube_user_id'])) ?>" download="report.csv" target="_blank" class="align-self-start btn btn-light"><i class="fas fa-file-csv"></i> <?= $language->global->export_csv ?></a>
        </div>
        <p class="text-muted"><?= $language->report->display->summary_help ?></p>

        <table class="table table-responsive-md">
            <thead class="thead-black bg-youtube">
            <tr>
                <th>
                    <?= $language->report->display->date ?>&nbsp;
                    <span data-toggle="tooltip" title="<?= sprintf($language->report->display->date_help, $language->global->date->datetime_format) ?>"><i class="fa fa-question-circle"></i></span>
                </th>
                <th></th>
                <th><?= $language->youtube->report->display->subscribers ?></th>
                <th></th>
                <th><?= $language->youtube->report->display->views ?></th>
                <th></th>
                <th><?= $language->youtube->report->display->videos ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total_new = [
                'subscribers'   => 0,
                'views'         => 0,
                'videos'         => 0
            ];
            for($i = 0; $i < count($logs); $i++):
                $log_yesterday = ($i == count($logs) - 1) ? false : $logs[$i+1];
                $log = $logs[$i];
                $log_date = (new \DateTime($log['date']))->format($language->global->date->datetime_format);
                $log_date_name = $language->global->date->short_day_of_week->{(new \DateTime($log['date']))->format('N')};
                $subscribers_difference = $log_yesterday ? $log['subscribers'] - $log_yesterday['subscribers'] : 0;
                $views_difference = $log_yesterday ? $log['views'] - $log_yesterday['views'] : 0;
                $videos_difference = $log_yesterday ? $log['videos'] - $log_yesterday['videos'] : 0;

                $total_new['subscribers'] += $subscribers_difference;
                $total_new['views'] += $views_difference;
                $total_new['videos'] += $videos_difference;
                ?>
                <tr>
                    <td><?= $log_date ?></td>
                    <td><?= $log_date_name ?></td>
                    <td><?= nr($log['subscribers']) ?></td>
                    <td><?= colorful_number($subscribers_difference) ?></td>
                    <td><?= nr($log['views']) ?></td>
                    <td><?= colorful_number($views_difference) ?></td>
                    <td><?= nr($log['videos']) ?></td>
                    <td><?= colorful_number($videos_difference) ?></td>
                </tr>
            <?php endfor ?>

                <tr class="bg-light">
                    <td colspan="2"><?= $language->report->display->total_summary ?></td>
                    <td colspan="2"><?= colorful_number($total_new['subscribers']) ?></td>
                    <td colspan="2"><?= colorful_number($total_new['views']) ?></td>
                    <td colspan="2"><?= colorful_number($total_new['videos']) ?></td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="margin-bottom-6">
        <h2><?= $language->report->display->projections ?></h2>
        <p class="text-muted"><?= $language->report->display->projections_help ?></p>

        <table class="table table-responsive-md">
            <thead class="thead-black">
            <tr>
                <th><?= $language->report->display->time_until ?></th>
                <th><?= $language->report->display->date ?></th>
                <th><?= $language->youtube->report->display->subscribers ?></th>
                <th><?= $language->youtube->report->display->views ?></th>
                <th><?= $language->youtube->report->display->videos ?></th>
            </tr>
            </thead>

            <tbody>
            <tr class="bg-light">
                <td><?= $language->report->display->time_until_now ?></td>
                <td><?= (new \DateTime())->format($language->global->date->datetime_format) ?></td>
                <td><?= nr($source_account->subscribers) ?></td>
                <td><?= nr($source_account->views) ?></td>
                <td><?= nr($source_account->videos) ?></td>
            </tr>

            <?php if($total_days < 2): ?>

                <tr class="bg-light">
                    <td colspan="4"><?= $language->report->display->no_projections ?></td>
                </tr>

            <?php else: ?>
                <tr>
                    <td><?= sprintf($language->global->date->x_days, 30) ?></td>
                    <td><?= (new \DateTime())->modify('+30 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->subscribers + ($average['subscribers'] * 30)) ?></td>
                    <td><?= nr($source_account->views + ($average['views'] * 30)) ?></td>
                    <td><?= nr($source_account->videos + ($average['videos'] * 30)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_days, 60) ?></td>
                    <td><?= (new \DateTime())->modify('+60 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->subscribers + ($average['subscribers'] * 60)) ?></td>
                    <td><?= nr($source_account->views + ($average['views'] * 60)) ?></td>
                    <td><?= nr($source_account->videos + ($average['videos'] * 60)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_months, 3) ?></td>
                    <td><?= (new \DateTime())->modify('+90 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->subscribers + ($average['subscribers'] * 90)) ?></td>
                    <td><?= nr($source_account->views + ($average['views'] * 90)) ?></td>
                    <td><?= nr($source_account->videos + ($average['videos'] * 90)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_months, 6) ?></td>
                    <td><?= (new \DateTime())->modify('+180 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->subscribers + ($average['subscribers'] * 180)) ?></td>
                    <td><?= nr($source_account->views + ($average['views'] * 180)) ?></td>
                    <td><?= nr($source_account->videos + ($average['videos'] * 180)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_months, 9) ?></td>
                    <td><?= (new \DateTime())->modify('+270 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->subscribers + ($average['subscribers'] * 270)) ?></td>
                    <td><?= nr($source_account->views + ($average['views'] * 270)) ?></td>
                    <td><?= nr($source_account->videos + ($average['videos'] * 270)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->year) ?></td>
                    <td><?= (new \DateTime())->modify('+365 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->subscribers + ($average['subscribers'] * 365)) ?></td>
                    <td><?= nr($source_account->views + ($average['views'] * 365)) ?></td>
                    <td><?= nr($source_account->videos + ($average['videos'] * 365)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->year_and_half) ?></td>
                    <td><?= (new \DateTime())->modify('+547 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->subscribers + ($average['subscribers'] * 547)) ?></td>
                    <td><?= nr($source_account->views + ($average['views'] * 547)) ?></td>
                    <td><?= nr($source_account->videos + ($average['videos'] * 547)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_years, 2, $language->global->number->decimal_point, $language->global->number->thousands_separator) ?></td>
                    <td><?= (new \DateTime())->modify('+730 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->subscribers + ($average['subscribers'] * 730)) ?></td>
                    <td><?= nr($source_account->views + ($average['views'] * 730)) ?></td>
                    <td><?= nr($source_account->videos + ($average['videos'] * 730)) ?></td>
                </tr>

                <tr class="bg-light">
                    <td colspan="2"><?= $language->report->display->average_calculations ?></td>
                    <td><?= sprintf($language->youtube->report->display->subscribers_per_day, colorful_number($average['subscribers'])) ?></td>
                    <td><?= sprintf($language->youtube->report->display->views_per_day, colorful_number($average['views'])) ?></td>
                    <td><?= sprintf($language->youtube->report->display->videos_per_day, colorful_number($average['videos'])) ?></td>
                </tr>

            <?php endif ?>
            </tbody>
        </table>
    </div>

    <?php if($settings->youtube_check_videos && count($videos_results)): ?>
    <div class="margin-bottom-6">
        <h2><?= $language->youtube->report->display->last_videos ?></h2>
        <p class="text-muted"><?= $language->youtube->report->display->last_videos_help ?></p>

        <table class="table table-responsive-md">
            <thead class="thead-black">
            <tr>
                <th></th>
                <th><?= $language->youtube->report->display->video_created_date ?></th>
                <th><?= $language->youtube->report->display->video_title ?></th>
                <th><?= $language->youtube->report->display->video_views ?></th>
                <th><?= $language->youtube->report->display->video_likes ?></th>
                <th><?= $language->youtube->report->display->video_dislikes ?></th>
                <th><?= $language->youtube->report->display->video_comments ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($videos_results as $video): ?>
                <tr>
                    <td>
                        <a href="https://youtube.com/watch?v=<?= $video->video_id ?>" target="_blank">
                            <img src="<?= $video->thumbnail_url ?>" class="img-responsive rounded-circle youtube-avatar-small" />
                        </a>
                    </td>
                    <td><span data-toggle="tooltip" title="<?= (new \DateTime($video->created_date))->format($language->global->date->datetime_format . ' H:i:s') ?>"><?= (new \DateTime($video->created_date))->format($language->global->date->datetime_format) ?></span></td>
                    <td><span data-toggle="tooltip" title="<?= $video->title ?>"><?= string_resize($video->title, 25) ?></span></td>
                    <td>
                        <i class="fa fa-eye views-color"></i> <?= nr($video->views) ?>
                    </td>
                    <td>
                        <i class="fa fa-heart like-color"></i> <?= nr($video->likes) ?>
                    </td>
                    <td>
                        <i class="fa fa-thumbs-down"></i> <?= nr($video->dislikes) ?>
                    </td>
                    <td>
                        <i class="fa fa-comments"></i> <?= nr($video->comments) ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>

    </div>

    <div class="margin-bottom-6">
        <h2><?= $language->youtube->report->display->videos_stats_chart ?></h2>

        <div class="chart-container">
            <canvas id="videos_chart"></canvas>
        </div>

    </div>
    <?php endif ?>

</div>


<script>
    /* Datepicker */
    $('#datepicker_input').datepicker({
        language: 'en',
        autoClose: true,
        timepicker: false,
        toggleSelected: false,
        minDate: new Date($('#datepicker_input').data('min')),
        maxDate: new Date()
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

    Chart.defaults.global.elements.line.borderWidth = 4;
    Chart.defaults.global.elements.point.radius = 3;
    Chart.defaults.global.elements.point.borderWidth = 7;


    let subscribers_chart_context = document.getElementById('subscribers_chart').getContext('2d');

    let gradient = subscribers_chart_context.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(43, 227, 155, 0.6)');
    gradient.addColorStop(1, 'rgba(43, 227, 155, 0.05)');

    new Chart(subscribers_chart_context, {
        type: 'line',
        data: {
            labels: <?= $logs_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($language->youtube->report->display->subscribers) ?>,
                data: <?= $logs_chart['subscribers'] ?>,
                backgroundColor: gradient,
                borderColor: '#2BE39B',
                fill: true
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: (tooltipItem, data) => {
                        let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];

                        return `${number_format(value, 0, '<?= $language->global->number->decimal_point ?>',  '<?= $language->global->number->thousands_separator ?>')} ${data.datasets[tooltipItem.datasetIndex].label}`;
                    }
                }
            },
            title: {
                text: <?= json_encode($language->youtube->report->display->subscribers_chart) ?>,
                display: true
            },
            legend: {
                display: false
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        userCallback: (value, index, values) => {
                            if(Math.floor(value) === value) {
                                return number_format(value, 0, '<?= $language->global->number->decimal_point ?>', '<?= $language->global->number->thousands_separator ?>');
                            }
                        }
                    }
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            }
        }
    });


    let views_videos_chart_context = document.getElementById('views_videos_chart').getContext('2d');

    gradient = views_videos_chart_context.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(62, 193, 255, 0.6)');
    gradient.addColorStop(1, 'rgba(62, 193, 255, 0.05)');

    gradient2 = views_videos_chart_context.createLinearGradient(0, 0, 0, 250);
    gradient2.addColorStop(0, 'rgba(81, 140, 255, 0.6)');
    gradient2.addColorStop(1, 'rgba(81, 140, 255, 0.05)');

    new Chart(views_videos_chart_context, {
        type: 'line',
        data: {
            labels: <?= $logs_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($language->youtube->report->display->views) ?>,
                data: <?= $logs_chart['views'] ?>,
                backgroundColor: gradient,
                borderColor: '#3ec1ff',
                fill: true
            },
            {
                label: <?= json_encode($language->youtube->report->display->videos) ?>,
                data: <?= $logs_chart['videos'] ?>,
                backgroundColor: gradient2,
                borderColor: '#518CFF',
                fill: true
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: (tooltipItem, data) => {
                        let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];

                        return `${number_format(value, 0, '<?= $language->global->number->decimal_point ?>',  '<?= $language->global->number->thousands_separator ?>')} ${data.datasets[tooltipItem.datasetIndex].label}`;
                    }
                }
            },
            title: {
                text: <?= json_encode($language->youtube->report->display->views_videos_chart) ?>,
                display: true
            },
            legend: {
                display: false
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        userCallback: (value, index, values) => {
                            if(Math.floor(value) === value) {
                                return number_format(value, 0, '<?= $language->global->number->decimal_point ?>', '<?= $language->global->number->thousands_separator ?>');
                            }
                        }
                    }
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    }
                }]
            }
        }
    });

    new Chart(document.getElementById('videos_chart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $videos_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($language->youtube->report->display->video_views) ?>,
                data: <?= $videos_chart['views'] ?>,
                backgroundColor: '#065fd4',
                borderColor: '#065fd4',
                fill: false
            },
            {
                label: <?= json_encode($language->youtube->report->display->video_likes) ?>,
                data: <?= $videos_chart['likes'] ?>,
                backgroundColor: '#ED4956',
                borderColor: '#ED4956',
                fill: false
            },
            {
                label: <?= json_encode($language->youtube->report->display->video_dislikes) ?>,
                data: <?= $videos_chart['dislikes'] ?>,
                backgroundColor: '#25f7b1',
                borderColor: '#25f7b1',
                fill: false
            },
            {
                label: <?= json_encode($language->youtube->report->display->video_comments) ?>,
                data: <?= $videos_chart['comments'] ?>,
                backgroundColor: '#062a55',
                borderColor: '#062a55',
                fill: false
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: (tooltipItem, data) => {
                        let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];

                        return `${number_format(value, 0, '<?= $language->global->number->decimal_point ?>',  '<?= $language->global->number->thousands_separator ?>')} ${data.datasets[tooltipItem.datasetIndex].label}`;
                    }
                }
            },
            title: {
                display: false
            },
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
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
</script>

