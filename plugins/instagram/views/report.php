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

        <div class="row">
            <div class="col">
                <h5>
                    <?= $language->instagram->report->display->engagement_rate ?>
                    <span data-toggle="tooltip" title="<?= $language->instagram->report->display->engagement_rate_help ?>"><i class="fa fa-question-circle text-muted"></i></span>
                </h5>
            </div>

            <div class="col">
                <span class="report-content-number"><?= nr($source_account->average_engagement_rate, 2) ?>%</span>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <h5>
                    <?= $language->instagram->report->display->average_likes ?>
                    <span data-toggle="tooltip" title="<?= sprintf($language->instagram->report->display->average_likes_help, $settings->instagram_calculator_media_count) ?>"> <i class="fa fa-heart like-color"></i></span>
                </h5>
            </div>

            <div class="col">
                <span class="report-content-number"><?= nr($source_account->details->average_likes) ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <h5>
                    <?= $language->instagram->report->display->average_comments ?>
                    <span data-toggle="tooltip" title="<?= sprintf($language->instagram->report->display->average_comments_help, $settings->instagram_calculator_media_count) ?>"><i class="fa fa-comments text-muted"></i></span>
                </h5>
            </div>

            <div class="col">
                <span class="report-content-number"><?= nr($source_account->details->average_comments) ?></span>
            </div>
        </div>
    </div>

    <?php if(count($logs) == 1): ?>
    <div class="alert alert-info mb-5" role="alert">
        <?= $language->report->info_message->recently_generated ?>
    </div>
    <?php endif ?>

    <div class="margin-bottom-6">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-baseline align-items-md-center">
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
            <canvas id="followers_chart"></canvas>
        </div>

        <div class="chart-container mt-3">
            <canvas id="following_chart"></canvas>
        </div>
    </div>


    <div class="margin-bottom-6">
        <div class="d-flex justify-content-between">
            <h2><?= $language->report->display->summary ?></h2>

            <a href="<?= csv_link_exporter(csv_exporter($logs, ['id', 'instagram_user_id'])) ?>" download="report.csv" target="_blank" class="align-self-start btn btn-light"><i class="fas fa-file-csv"></i> <?= $language->global->export_csv ?></a>
        </div>
        <p class="text-muted"><?= $language->report->display->summary_help ?></p>

        <table class="table table-responsive-md">
            <thead class="thead-black bg-instagram">
            <tr>
                <th>
                    <?= $language->report->display->date ?>&nbsp;
                    <span data-toggle="tooltip" title="<?= sprintf($language->report->display->date_help, $language->global->date->datetime_format) ?>"><i class="fa fa-question-circle text-muted"></i></span>
                </th>
                <th></th>
                <th><?= $language->instagram->report->display->followers ?></th>
                <th></th>
                <th><?= $language->instagram->report->display->following ?></th>
                <th></th>
                <th><?= $language->instagram->report->display->uploads ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total_new = [
                'followers' => 0,
                'uploads' => 0
            ];
            for($i = 0; $i < count($logs); $i++):
                $log_yesterday = ($i == count($logs) - 1) ? false : $logs[$i+1];
                $log = $logs[$i];
                $log_date = (new \DateTime($log['date']))->format($language->global->date->datetime_format);
                $log_date_name = $language->global->date->short_day_of_week->{(new \DateTime($log['date']))->format('N')};
                $followers_difference = $log_yesterday ? $log['followers'] - $log_yesterday['followers'] : 0;
                $following_difference = $log_yesterday ? $log['following'] - $log_yesterday['following'] : 0;
                $uploads_difference = $log_yesterday ? $log['uploads'] - $log_yesterday['uploads'] : 0;

                $total_new['followers'] += $followers_difference;
                $total_new['uploads'] += $uploads_difference;
                ?>
                <tr>
                    <td><?= $log_date ?></td>
                    <td><?= $log_date_name ?></td>
                    <td><?= nr($log['followers']) ?></td>
                    <td><?= colorful_number($followers_difference) ?></td>
                    <td><?= nr($log['following']) ?></td>
                    <td><?= colorful_number($following_difference) ?></td>
                    <td><?= nr($log['uploads']) ?></td>
                    <td><?= colorful_number($uploads_difference) ?></td>
                </tr>
            <?php endfor ?>

            <tr class="bg-light">
                <td colspan="2"><?= $language->report->display->total_summary ?></td>
                <td colspan="4"><?= colorful_number($total_new['followers']) ?></td>
                <td colspan="2"><?= colorful_number($total_new['uploads']) ?></td>
            </tr>


            </tbody>
        </table>
    </div>

    <div class="margin-bottom-6">
        <h2><?= $language->instagram->report->display->average_engagement_rate_chart_summary ?></h2>
        <p class="text-muted"><?= $language->instagram->report->display->average_engagement_rate_chart_summary_help ?></p>

        <div class="chart-container">
            <canvas id="average_engagement_rate_chart"></canvas>
        </div>

    </div>

    <div class="margin-bottom-6">
        <h2><?= $language->report->display->projections ?></h2>
        <p class="text-muted"><?= $language->report->display->projections_help ?></p>

        <table class="table table-responsive-md">
            <thead class="thead-black">
            <tr>
                <th><?= $language->report->display->time_until ?></th>
                <th><?= $language->report->display->date ?></th>
                <th><?= $language->instagram->report->display->followers ?></th>
                <th><?= $language->instagram->report->display->uploads ?></th>
            </tr>
            </thead>

            <tbody>
            <tr class="bg-light">
                <td><?= $language->report->display->time_until_now ?></td>
                <td><?= (new \DateTime())->format($language->global->date->datetime_format) ?></td>
                <td><?= nr($source_account->followers) ?></td>
                <td><?= nr($source_account->uploads) ?></td>
            </tr>

            <?php if($total_days < 2): ?>

                <tr class="bg-light">
                    <td colspan="4"><?= $language->report->display->no_projections ?></td>
                </tr>

            <?php else: ?>
                <tr>
                    <td><?= sprintf($language->global->date->x_days, 30) ?></td>
                    <td><?= (new \DateTime())->modify('+30 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->followers + ($average['followers'] * 30)) ?></td>
                    <td><?= nr($source_account->uploads + ($average['uploads'] * 30)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_days, 60) ?></td>
                    <td><?= (new \DateTime())->modify('+60 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->followers + ($average['followers'] * 60)) ?></td>
                    <td><?= nr($source_account->uploads + ($average['uploads'] * 60)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_months, 3) ?></td>
                    <td><?= (new \DateTime())->modify('+90 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->followers + ($average['followers'] * 90)) ?></td>
                    <td><?= nr($source_account->uploads + ($average['uploads'] * 90)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_months, 6) ?></td>
                    <td><?= (new \DateTime())->modify('+180 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->followers + ($average['followers'] * 180)) ?></td>
                    <td><?= nr($source_account->uploads + ($average['uploads'] * 180)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_months, 9) ?></td>
                    <td><?= (new \DateTime())->modify('+270 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->followers + ($average['followers'] * 270)) ?></td>
                    <td><?= nr($source_account->uploads + ($average['uploads'] * 270)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->year) ?></td>
                    <td><?= (new \DateTime())->modify('+365 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->followers + ($average['followers'] * 365)) ?></td>
                    <td><?= nr($source_account->uploads + ($average['uploads'] * 365)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->year_and_half) ?></td>
                    <td><?= (new \DateTime())->modify('+547 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->followers + ($average['followers'] * 547)) ?></td>
                    <td><?= nr($source_account->uploads + ($average['uploads'] * 547)) ?></td>
                </tr>

                <tr>
                    <td><?= sprintf($language->global->date->x_years, 2, $language->global->number->decimal_point, $language->global->number->thousands_separator) ?></td>
                    <td><?= (new \DateTime())->modify('+730 day')->format($language->global->date->datetime_format) ?></td>
                    <td><?= nr($source_account->followers + ($average['followers'] * 730)) ?></td>
                    <td><?= nr($source_account->uploads + ($average['uploads'] * 730)) ?></td>
                </tr>

                <tr class="bg-light">
                    <td colspan="2"><?= $language->report->display->average_calculations ?></td>
                    <td><?= sprintf($language->instagram->report->display->followers_per_day, colorful_number($average['followers'])) ?></td>
                    <td><?= sprintf($language->instagram->report->display->uploads_per_day, colorful_number($average['uploads'])) ?></td>
                </tr>

            <?php endif ?>
            </tbody>
        </table>
    </div>

    <?php if(count((array) $source_account->details->top_posts) > 0): ?>
    <div class="margin-bottom-6 d-print-none">

        <h2><?= $language->instagram->report->display->top_posts ?></h2>
        <p class="text-muted"><?= sprintf($language->instagram->report->display->top_posts_help, $settings->instagram_calculator_media_count) ?></p>

        <div class="row mb-5">
            <?php foreach($source_account->details->top_posts as $shortcode => $engagement_rate): ?>

                <div class="col-sm-12 col-md-6 col-lg-4">

                    <?= InstagramHelper::get_embed_html($shortcode) ?>

                </div>


            <?php endforeach ?>
        </div>
    </div>
    <?php endif ?>


    <?php if(count((array) $source_account->details->top_mentions) > 0 || count((array) $source_account->details->top_hashtags) > 0): ?>
    <div class="margin-bottom-6">
        <div class="row">
            <?php if(count((array) $source_account->details->top_mentions) > 0): ?>
                <div class="col">
                    <h2><?= $language->instagram->report->display->top_mentions ?></h2>
                    <p class="text-muted"><?= sprintf($language->instagram->report->display->top_mentions_help, $settings->instagram_calculator_media_count) ?></p>

                    <div class="d-flex flex-column">
                        <?php foreach((array) $source_account->details->top_mentions as $mention => $use): ?>
                            <div class="d-flex align-items-center">

                                <a href="https://www.instagram.com/<?= $mention ?>" class="text-dark report-content-number-link" target="_blank"><?= $mention ?></a>

                                <span class="report-content-number" data-toggle="tooltip" title="<?= sprintf($language->instagram->report->display->mention_use, $use, $settings->instagram_calculator_media_count) ?>"><?= $use ?></span>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>

            <?php if(count((array) $source_account->details->top_hashtags) > 0): ?>
                <div class="col">
                    <h2><?= $language->instagram->report->display->top_hashtags ?></h2>
                    <p class="text-muted"><?= sprintf($language->instagram->report->display->top_hashtags_help, $settings->instagram_calculator_media_count) ?></p>


                    <div class="d-flex flex-column">
                        <?php foreach((array) $source_account->details->top_hashtags as $hashtag => $use): ?>
                            <div class="d-flex align-items-center">

                                <a href="https://www.instagram.com/explore/tags/<?= $hashtag ?>/" class="text-dark report-content-number-link" target="_blank">#<?= $hashtag ?></a>

                                <span class="report-content-number" data-toggle="tooltip" title="<?= sprintf($language->instagram->report->display->hashtag_use, $use, $settings->instagram_calculator_media_count) ?>"><?= $use ?></span>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
    <?php endif ?>


    <div class="margin-bottom-6">
        <h2><?= $language->report->statistics_compare->title ?></h2>
        <p class="text-muted"><?= $language->report->statistics_compare->subtitle ?></p>

        <?php

        $report_engagement = '<img src="' . $source_account->profile_picture_url . '" class="img-responsive rounded-circle instagram-avatar-small" alt="' . $source_account->full_name . '" />&nbsp;' . '<strong>' . nr($source_account->average_engagement_rate, 2) . '%</strong>';

        ?>

        <table class="table table-responsive-md">
            <thead class="thead-black">
            <tr>
                <th><?= $language->instagram->report->display->followers ?></th>
                <th><?= $language->instagram->report->display->engagement ?></th>
                <th><?= $language->instagram->report->display->profile_engagement ?></th>
            </tr>
            </thead>

            <tbody>
            <tr <?php if($source_account->followers < 1000) echo 'class="bg-light"' ?>>
                <td>< 1,000</td>
                <td>8%</td>
                <td>
                    <?php if($source_account->followers < 1000): ?>

                        <?= $report_engagement ?>

                    <?php endif ?>
                </td>
            </tr>

            <tr <?php if($source_account->followers >= 1000 && $source_account->followers < 5000) echo 'class="bg-light"' ?>>
                <td>< 5,000</td>
                <td>5.7%</td>
                <td>
                    <?php if($source_account->followers >= 1000 && $source_account->followers < 5000): ?>

                        <?= $report_engagement ?>

                    <?php endif ?>
                </td>
            </tr>

            <tr <?php if($source_account->followers >= 5000 && $source_account->followers < 10000) echo 'class="bg-light"' ?>>
                <td>< 10,000</td>
                <td>4%</td>
                <td>
                    <?php if($source_account->followers >= 5000 && $source_account->followers < 10000): ?>

                        <?= $report_engagement ?>

                    <?php endif ?>
                </td>
            </tr>

            <tr <?php if($source_account->followers >= 10000 && $source_account->followers < 100000) echo 'class="bg-light"' ?>>
                <td>< 100,000</td>
                <td>2.4%</td>
                <td>
                    <?php if($source_account->followers >= 10000 && $source_account->followers < 100000): ?>

                        <?= $report_engagement ?>

                    <?php endif ?>
                </td>
            </tr>

            <tr <?php if($source_account->followers >= 100000) echo 'class="bg-light"' ?>>
                <td>100,000+</td>
                <td>1.7%</td>
                <td>
                    <?php if($source_account->followers >= 100000): ?>

                        <?= $report_engagement ?>

                    <?php endif ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <?php if($instagram_media_result->num_rows): ?>
        <div class="margin-bottom-6">
            <h2><?= $language->instagram->report->display->media_summary ?></h2>
            <p class="text-muted"><?= sprintf($language->instagram->report->display->media_summary_help, $settings->instagram_calculator_media_count) ?></p>

            <table class="table table-responsive-md">
                <thead class="thead-black">
                <tr>
                    <th></th>
                    <th></th>
                    <th><?= $language->instagram->report->display->media_created_date ?></th>
                    <th><?= $language->instagram->report->display->media_caption ?></th>
                    <th><?= $language->instagram->report->display->media_likes ?></th>
                    <th><?= $language->instagram->report->display->media_comments ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($media_results as $media): ?>
                    <tr>
                        <td>
                            <a href="<?= $media->media_url ?>" target="_blank" data-toggle="tooltip" title="<?= $language->instagram->report->display->media_type->{strtolower($media->type)} ?>">
                                <?php
                                switch($media->type) {
                                    case 'IMAGE':
                                        echo '<i class="fa fa-image"></i>';
                                        break;

                                    case 'VIDEO':
                                        echo '<i class="fa fa-video"></i>';
                                        break;

                                    case 'SIDECAR':
                                        echo '<i class="fa fa-images"></i>';
                                        break;

                                }
                                ?>
                            </a>
                        </td>
                        <td><img src="<?= $media->media_image_url ?>" class="img-responsive rounded-circle instagram-avatar-small" /></td>
                        <td><span data-toggle="tooltip" title="<?= (new \DateTime())->setTimestamp($media->created_date)->format($language->global->date->datetime_format . ' H:i:s') ?>"><?= (new \DateTime())->setTimestamp($media->created_date)->format($language->global->date->datetime_format) ?></span></td>
                        <td><span data-toggle="tooltip" title="<?= $media->caption ?>"><?= string_resize($media->caption, 25) ?></span></td>
                        <td>
                            <i class="fa fa-heart like-color"></i> <?= nr($media->likes) ?>
                            <small><?= colorful_number(get_percentage_difference($source_account->details->average_likes, $media->likes), '%') ?></small>
                        </td>
                        <td>
                            <i class="fa fa-comments"></i> <?= nr($media->comments) ?>
                            <small><?= colorful_number(get_percentage_difference($source_account->details->average_comments, $media->comments), '%') ?></small>
                        </td>
                    </tr>
                <?php endforeach ?>

                </tbody>
            </table>
        </div>

        <div class="margin-bottom-6">
            <h2><?= $language->instagram->report->display->media_stats_chart ?></h2>

            <div class="chart-container">
                <canvas id="media_chart"></canvas>
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


    let followers_chart_context = document.getElementById('followers_chart').getContext('2d');

    let gradient = followers_chart_context.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(43, 227, 155, 0.6)');
    gradient.addColorStop(1, 'rgba(43, 227, 155, 0.05)');

    new Chart(followers_chart_context, {
        type: 'line',
        data: {
            labels: <?= $logs_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($language->instagram->report->display->followers) ?>,
                data: <?= $logs_chart['followers'] ?>,
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
                text: <?= json_encode($language->instagram->report->display->followers_chart) ?>,
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


    let following_chart_context = document.getElementById('following_chart').getContext('2d');

    gradient = following_chart_context.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(62, 193, 255, 0.6)');
    gradient.addColorStop(1, 'rgba(62, 193, 255, 0.05)');

    new Chart(following_chart_context, {
        type: 'line',
        data: {
            labels: <?= $logs_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($language->instagram->report->display->following) ?>,
                data: <?= $logs_chart['following'] ?>,
                backgroundColor: gradient,
                borderColor: '#3ec1ff',
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
                text: <?= json_encode($language->instagram->report->display->following_chart) ?>,
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


    let average_engagement_rate_chart_context = document.getElementById('average_engagement_rate_chart').getContext('2d');

    gradient = average_engagement_rate_chart_context.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(237, 73, 86, 0.4)');
    gradient.addColorStop(1, 'rgba(237, 73, 86, 0.05)');

    let average_engagement_rate_chart = new Chart(average_engagement_rate_chart_context, {
        type: 'line',
        data: {
            labels: <?= $logs_chart['labels'] ?>,
            datasets: [{
                data: <?= $logs_chart['average_engagement_rate'] ?>,
                backgroundColor: gradient,
                borderColor: '#ED4956',
                fill: true
            }]
        },
        options: {
            tooltips: {
                mode: 'index',
                intersect: false
            },
            title: {
                text: <?= json_encode($language->instagram->report->display->average_engagement_rate_chart) ?>,
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


    new Chart(document.getElementById('media_chart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= $media_chart['labels'] ?>,
            datasets: [{
                label: <?= json_encode($language->instagram->report->display->media_likes) ?>,
                data: <?= $media_chart['likes'] ?>,
                backgroundColor: '#ED4956',
                borderColor: '#ED4956',
                fill: false
            },
                {
                    label: <?= json_encode($language->instagram->report->display->media_comments) ?>,
                    data: <?= $media_chart['comments'] ?>,
                    backgroundColor: '#2caff7',
                    borderColor: '#2caff7',
                    fill: false
                },
                {
                    label: <?= json_encode($language->instagram->report->display->media_caption_count) ?>,
                    data: <?= $media_chart['captions'] ?>,
                    backgroundColor: '#25f7b1',
                    borderColor: '#25f7b1',
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

</script>
