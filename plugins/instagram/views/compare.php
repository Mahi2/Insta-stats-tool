<?php defined('ALTUMCODE') || die() ?>

<h2><?= $language->compare->header ?></h2>
<p class="text-muted"><?= $language->compare->header_help ?></p>

<div class="compare-search">
    <form class="form-inline d-inline-flex justify-content-center" action="" method="GET" id="compare_search_form">
        <input class="form-control compare-search-input" type="search" id="user_one" value="<?= $user_one ?>" placeholder="<?= $language->compare->display->search_input_placeholder ?>" aria-label="<?= $language->compare->display->search_input_placeholder ?>" required="required">

        <span class="mx-3"><?= $language->compare->display->compare_text ?></span>

        <input class="form-control compare-search-input" type="search" id="user_two" value="<?= $user_two ?>" placeholder="<?= $language->compare->display->search_input_placeholder ?>" aria-label="<?= $language->compare->display->search_input_placeholder ?>" required="required">

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
                        <a href="<?= 'https://instagram.com/'.$source_account_one->username ?>" target="_blank" class="text-dark" rel="nofollow"><?= '@' . $source_account_one->username ?></a>
                    </p>

                    <h3 class="text-right">
                        <?= $source_account_one->full_name ?>

                        <?php if($source_account_one->is_private): ?>
                            <span data-toggle="tooltip" title="<?= $language->report->display->private ?>"><i class="fa fa-lock user-private-badge"></i></span>
                        <?php endif ?>

                        <?php if($source_account_one->is_verified): ?>
                            <span data-toggle="tooltip" title="<?= $language->instagram->report->display->verified ?>"><i class="fa fa-check-circle user-verified-badge"></i></span>
                        <?php endif ?>

                    </h3>

                    <div class="d-flex justify-content-end">
                        <a href="report/<?= $user_one ?>/instagram" class="btn btn-default btn-sm"><?= $language->compare->display->view_report ?></a>
                    </div>
                </div>

                <img src="<?= $source_account_one->profile_picture_url ?>" onerror="$(this).attr('src', ($(this).data('failover')))" data-failover="<?= $settings->url . ASSETS_ROUTE ?>images/default_avatar.png" class="img-responsive rounded-circle instagram-avatar ml-3" alt="<?= $source_account_one->full_name ?>" />
            </div>

        </div>

        <div class="col-12 col-md-1 d-flex justify-content-center">
            <?= $language->compare->display->compare_text ?>
        </div>

        <div class="col">
            <div class="d-flex">

                <img src="<?= $source_account_two->profile_picture_url ?>" onerror="$(this).attr('src', ($(this).data('failover')))" data-failover="<?= $settings->url . ASSETS_ROUTE ?>images/default_avatar.png" class="img-responsive rounded-circle instagram-avatar mr-3" alt="<?= $source_account_two->full_name ?>" />

                <div class="d-flex flex-column justify-content-center">
                    <p class="m-0">
                        <a href="<?= 'https://instagram.com/'.$source_account_two->username ?>" target="_blank" class="text-dark" rel="nofollow"><?= '@' . $source_account_two->username ?></a>
                    </p>

                    <h3>
                        <?= $source_account_two->full_name ?>

                        <?php if($source_account_two->is_private): ?>
                            <span data-toggle="tooltip" title="<?= $language->report->display->private ?>"><i class="fa fa-lock user-private-badge"></i></span>
                        <?php endif ?>

                        <?php if($source_account_two->is_verified): ?>
                            <span data-toggle="tooltip" title="<?= $language->instagram->report->display->verified ?>"><i class="fa fa-check-circle user-verified-badge"></i></span>
                        <?php endif ?>

                    </h3>

                    <div>
                        <a href="report/<?= $user_two ?>/instagram" class="btn btn-default btn-sm"><?= $language->compare->display->view_report ?></a>
                    </div>
                </div>

            </div>

        </div>

    </div>


    <div class="mt-5">
        <h2><?= $language->compare->display->statistics ?></h2>

        <table class="table table-responsive-md">
            <thead class="thead-black">
            <tr>
                <th class="th-33"></th>

                <th class="th-33">
                    <?= '@' . $source_account_one->username ?>
                </th>

                <th class="th-33">
                    <?= '@' . $source_account_two->username ?>
                </th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <th>
                    <?= $language->compare->display->engagement_rate ?>
                    <span data-toggle="tooltip" title="<?= $language->compare->display->engagement_rate_help ?>"><i class="fa fa-question-circle text-muted"></i></span>
                </th>

                <td class="<?= ($first_success = $source_account_one->average_engagement_rate > $source_account_two->average_engagement_rate) ? 'table-success' : 'table-danger' ?>">
                    <?= nr($source_account_one->average_engagement_rate, 2) ?>%
                </td>

                <td class="<?= !$first_success ? 'table-success' : 'table-danger' ?>">
                    <?= nr($source_account_two->average_engagement_rate, 2) ?>%
                </td>
            </tr>

            <tr>
                <th>
                    <?= $language->compare->display->average_likes ?>
                    <span data-toggle="tooltip" title="<?= sprintf($language->compare->display->average_likes_help, $settings->instagram_calculator_media_count) ?>"><i class="fa fa-thumbs-up text-muted"></i></span>
                </th>

                <td class="<?= ($first_success = $source_account_one_details->average_likes > $source_account_two_details->average_likes) ? 'table-success' : 'table-danger' ?>">
                    <?= nr($source_account_one_details->average_likes) ?>
                </td>

                <td class="<?= !$first_success ? 'table-success' : 'table-danger' ?>">
                    <?= nr($source_account_two_details->average_likes) ?>
                </td>
            </tr>

            <tr>
                <th>
                    <?= $language->compare->display->average_comments ?>
                    <span data-toggle="tooltip" title="<?= sprintf($language->compare->display->average_comments_help, $settings->instagram_calculator_media_count) ?>"><i class="fa fa-comments text-muted"></i></span>
                </th>

                <td class="<?= ($first_success = $source_account_one_details->average_comments > $source_account_two_details->average_comments) ? 'table-success' : 'table-danger' ?>">
                    <?= nr($source_account_one_details->average_comments) ?>
                </td>

                <td class="<?= !$first_success ? 'table-success' : 'table-danger' ?>">
                    <?= nr($source_account_two_details->average_comments) ?>
                </td>
            </tr>

            </tbody>
        </table>
    </div>


    <div class="mt-5">
        <h2><?= $language->compare->display->followers_chart ?></h2>

        <div class="chart-container">
            <canvas id="followers_chart"></canvas>
        </div>
    </div>

    <div class="mt-5">
        <h2><?= $language->compare->display->average_engagement_rate_chart ?></h2>

        <div class="chart-container">
            <canvas id="average_engagement_rate_chart"></canvas>
        </div>
    </div>

    <div class="mt-5">
        <h2><?= $language->compare->display->top_posts ?></h2>
        <div class="text-muted"><?= sprintf($language->compare->display->top_posts_help, $settings->instagram_calculator_media_count) ?></div>

        <div class="row">
            <?php if($source_account_one->is_private): ?>
                <div class="col"><?= $language->compare->info_message->private_account ?></div>
            <?php else: ?>
                <?php foreach($source_account_one_details->top_posts as $shortcode => $engagement_rate): ?>

                    <div class="col-sm-12 col-md-4">

                        <?php
                        $embed = InstagramHelper::get_embed_html($shortcode);

                        if($embed) {
                            echo $embed;
                        } else {
                            echo $language->compare->error_message->embed;
                        }

                        ?>
                    </div>

                <?php endforeach ?>
            <?php endif ?>
        </div>

        <div class="row mt-5">
            <?php if($source_account_two->is_private): ?>
                <div class="col"><?= $language->compare->info_message->private_account ?></div>
            <?php else: ?>
                <?php foreach($source_account_two_details->top_posts as $shortcode => $engagement_rate): ?>

                    <div class="col-sm-12 col-md-4">

                        <?php
                        $embed = InstagramHelper::get_embed_html($shortcode);

                        if($embed) {
                            echo $embed;
                        } else {
                            echo $language->compare->error_message->embed;
                        }

                        ?>

                    </div>

                <?php endforeach ?>
            <?php endif ?>
        </div>
    </div>

    <script>
        Chart.defaults.global.elements.line.borderWidth = 4;
        Chart.defaults.global.elements.point.radius = 3;
        Chart.defaults.global.elements.point.borderWidth = 7;

        new Chart(document.getElementById('followers_chart').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= $chart_labels ?>,
                datasets: [{
                    label: '<?= $user_one ?>',
                    data: <?= $chart_followers_one ?>,
                    backgroundColor: '#ED4956',
                    borderColor: '#ED4956',
                    fill: false
                },
                    {
                        label: '<?= $user_two ?>',
                        data: <?= $chart_followers_two ?>,
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

        new Chart(document.getElementById('average_engagement_rate_chart').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= $chart_labels ?>,
                datasets: [{
                    label: '<?= $user_one ?>',
                    data: <?= $chart_average_engagement_rate_one ?>,
                    backgroundColor: '#ED4956',
                    borderColor: '#ED4956',
                    fill: false
                },
                    {
                        label: '<?= $user_two ?>',
                        data: <?= $chart_average_engagement_rate_two ?>,
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
