<?php defined('ALTUMCODE') || die() ?>

<div class="card card-shadow mb-3">
    <div class="card-body">
        <ul class="nav nav-pills" role="tablist">
            <li class="nav-item"><a class="nav-link active" href="#main" data-toggle="pill" role="tab"><?= $language->admin_website_settings->tab->main ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#store" data-toggle="pill" role="tab"><?= $language->admin_website_settings->tab->store ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#ads" data-toggle="pill" role="tab"><?= $language->admin_website_settings->tab->ads ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#api" data-toggle="pill" role="tab"><?= $language->admin_website_settings->tab->api ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#social" data-toggle="pill" role="tab"><?= $language->admin_website_settings->tab->social ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#sources" data-toggle="pill" role="tab"><?= $language->admin_website_settings->tab->sources ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#cron" data-toggle="pill" role="tab"><?= $language->admin_website_settings->tab->cron ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#email" data-toggle="pill" role="tab"><?= $language->admin_website_settings->tab->email ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#email_templates" data-toggle="pill" role="tab"><?= $language->admin_website_settings->tab->email_templates ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#email_notifications" data-toggle="pill" role="tab"><?= $language->admin_website_settings->tab->email_notifications ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#proxy" data-toggle="pill" role="tab"><?= $language->admin_website_settings->tab->proxy ?></a></li>
        </ul>
    </div>
</div>

<div class="card card-shadow">
    <div class="card-body">


        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />

            <div class="tab-content">
                <div class="tab-pane fade show active" id="main">
                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->title ?></label>
                        <input type="text" name="title" class="form-control" value="<?= $settings->title ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->default_language ?></label>
                        <select name="default_language" class="form-control">
                            <?php foreach($languages as $value) echo '<option value="' . $value . '" ' . (($settings->default_language == $value) ? 'selected' : null) . '>' . $value . '</option>' ?>
                        </select>
                        <small class="text-muted"><?= $language->admin_website_settings->input->default_language_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->logo ?></label>
                        <?php if($settings->logo != ''): ?>
                        <div class="m-1">
                            <img src="<?= $settings->url . UPLOADS_ROUTE . 'logo/' . $settings->logo ?>" class="img-fluid navbar-logo" />
                        </div>
                        <?php endif ?>
                        <input id="logo-file-input" type="file" name="logo" class="form-control" />
                        <small class="text-muted"><?= $language->admin_website_settings->input->logo_help ?></small>
                        <small class="text-muted"><a href="admin/website-settings/remove-logo/<?= Security::csrf_get_session_token('url_token') ?>"><?= $language->admin_website_settings->input->logo_remove ?></a></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->favicon ?></label>
                        <?php if($settings->favicon != ''): ?>
                            <div class="m-1">
                                <img src="<?= $settings->url . UPLOADS_ROUTE . 'favicon/' . $settings->favicon ?>" class="img-fluid" />
                            </div>
                        <?php endif ?>
                        <input id="favicon-file-input" type="file" name="favicon" class="form-control" />
                        <small class="text-muted"><?= $language->admin_website_settings->input->favicon_help ?></small>
                        <small class="text-muted"><a href="admin/website-settings/remove-favicon/<?= Security::csrf_get_session_token('url_token') ?>"><?= $language->admin_website_settings->input->favicon_remove ?></a></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->meta_description ?></label>
                        <input type="text" name="meta_description" class="form-control" value="<?= $settings->meta_description ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->meta_keywords ?></label>
                        <input type="text" name="meta_keywords" class="form-control" value="<?= $settings->meta_keywords ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->time_zone ?></label>
                        <select name="time_zone" class="form-control">
                            <?php foreach(DateTimeZone::listIdentifiers() as $time_zone) echo '<option value="' . $time_zone . '" ' . (($settings->time_zone == $time_zone) ? 'selected' : null) . '>' . $time_zone . '</option>' ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->directory ?></label>

                        <select name="directory" class="custom-select form-control">
                            <option value="ALL" <?= ($settings->directory == 'ALL') ? 'selected' : null ?>>ALL</option>
                            <option value="LOGGED_IN" <?= ($settings->directory == 'LOGGED_IN') ? 'selected' : null ?>>LOGGED IN</option>
                            <option value="DISABLED" <?= ($settings->directory == 'DISABLED') ? 'selected' : null ?>>DISABLED</option>
                        </select>

                        <small class="text-muted"><?= $language->admin_website_settings->input->directory_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->directory_pagination ?></label>
                        <input type="text" name="directory_pagination" class="form-control" value="<?= $settings->directory_pagination ?>" />
                        <small class="text-muted"><?= $language->admin_website_settings->input->directory_pagination_help ?></small>
                    </div>
                </div>


                <div class="tab-pane fade" id="store">
                    <p class="text-muted"><?= $language->admin_website_settings->input->store_help ?></p>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->store_paypal_mode ?></label>

                        <select name="store_paypal_mode" class="custom-select form-control">
                            <option value="live" <?= ($settings->store_paypal_mode == 'live') ? 'selected' : null ?>>live</option>
                            <option value="sandbox" <?= ($settings->store_paypal_mode == 'sandbox') ? 'selected' : null ?>>sandbox</option>
                        </select>

                        <small class="text-muted"><?= $language->admin_website_settings->input->store_paypal_mode_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->store_paypal_client_id ?></label>
                        <input type="text" name="store_paypal_client_id" class="form-control" value="<?= $settings->store_paypal_client_id ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->store_paypal_secret ?></label>
                        <input type="text" name="store_paypal_secret" class="form-control" value="<?= $settings->store_paypal_secret ?>" />
                    </div>

                    <hr />

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->store_stripe_publishable_key ?></label>
                        <input type="text" name="store_stripe_publishable_key" class="form-control" value="<?= $settings->store_stripe_publishable_key ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->store_stripe_secret_key ?></label>
                        <input type="text" name="store_stripe_secret_key" class="form-control" value="<?= $settings->store_stripe_secret_key ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->store_stripe_webhook_secret ?></label>
                        <input type="text" name="store_stripe_webhook_secret" class="form-control" value="<?= $settings->store_stripe_webhook_secret ?>" />
                    </div>

                    <hr />

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->store_currency ?></label>
                        <input type="text" name="store_currency" class="form-control" value="<?= $settings->store_currency ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->store_unlock_report_price ?></label>
                        <input type="text" name="store_unlock_report_price" class="form-control" value="<?= $settings->store_unlock_report_price ?>" />
                        <small class="form-text text-muted"><?= $language->admin_website_settings->input->store_unlock_report_price_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->store_unlock_report_time ?></label>
                        <input type="text" name="store_unlock_report_time" class="form-control" value="<?= $settings->store_unlock_report_time ?>" />
                        <small class="form-text text-muted"><?= $language->admin_website_settings->input->store_unlock_report_time_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->store_no_ads_price ?></label>
                        <input type="text" name="store_no_ads_price" class="form-control" value="<?= $settings->store_no_ads_price ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->store_user_default_points ?></label>
                        <input type="text" name="store_user_default_points" class="form-control" value="<?= $settings->store_user_default_points ?>" />
                        <small class="form-text text-muted"><?= $language->admin_website_settings->input->store_user_default_points_help ?></small>
                    </div>
                </div>

                <div class="tab-pane fade" id="ads">
                    <p class="text-muted"><?= $language->admin_website_settings->input->ads_help ?></p>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->report_ad ?></label>
                        <textarea class="form-control" name="report_ad"><?= $settings->report_ad ?></textarea>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->index_ad ?></label>
                        <textarea class="form-control" name="index_ad"><?= $settings->index_ad ?></textarea>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->account_sidebar_ad ?></label>
                        <textarea class="form-control" name="account_sidebar_ad"><?= $settings->account_sidebar_ad ?></textarea>
                    </div>
                </div>

                <div class="tab-pane fade" id="api">
                    <p class="text-muted"><?= $language->admin_website_settings->input->store_help ?></p>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->recaptcha ?></label>

                        <select class="custom-select" name="recaptcha">
                            <option value="1" <?php if($settings->recaptcha) echo 'selected' ?>><?= $language->global->yes ?></option>
                            <option value="0" <?php if(!$settings->recaptcha) echo 'selected' ?>><?= $language->global->no ?></option>
                        </select>
                        <small class="text-muted"><?= $language->admin_website_settings->input->recaptcha_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->public_key ?></label>
                        <input type="text" name="public_key" class="form-control" value="<?= $settings->public_key ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->private_key ?></label>
                        <input type="text" name="private_key" class="form-control" value="<?= $settings->private_key ?>" />
                    </div>

                    <hr />

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->facebook_login ?></label>

                        <select class="custom-select" name="facebook_login">
                            <option value="1" <?php if($settings->facebook_login) echo 'selected' ?>><?= $language->global->yes ?></option>
                            <option value="0" <?php if(!$settings->facebook_login) echo 'selected' ?>><?= $language->global->no ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->facebook_app_id ?></label>
                        <input type="text" name="facebook_app_id" class="form-control" value="<?= $settings->facebook_app_id ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->facebook_app_secret ?></label>
                        <input type="text" name="facebook_app_secret" class="form-control" value="<?= $settings->facebook_app_secret ?>" />
                    </div>

                    <hr />

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->instagram_login ?></label>

                        <select class="custom-select" name="instagram_login">
                            <option value="1" <?php if($settings->instagram_login) echo 'selected' ?>><?= $language->global->yes ?></option>
                            <option value="0" <?php if(!$settings->instagram_login) echo 'selected' ?>><?= $language->global->no ?></option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->instagram_client_id ?></label>
                        <input type="text" name="instagram_client_id" class="form-control" value="<?= $settings->instagram_client_id ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->instagram_client_secret ?></label>
                        <input type="text" name="instagram_client_secret" class="form-control" value="<?= $settings->instagram_client_secret ?>" />
                    </div>

                    <hr />

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->analytics_code ?></label>
                        <input type="text" name="analytics_code" class="form-control" value="<?= $settings->analytics_code ?>" />
                        <small class="text-muted"><?= $language->admin_website_settings->input->analytics_code_help ?></small>
                    </div>

                </div>

                <div class="tab-pane fade" id="social">
                    <p class="text-muted"><?= $language->admin_website_settings->input->social_help ?></p>

                    <div class="form-group">
                        <label><i class="fab fa-facebook"></i> <?= $language->admin_website_settings->input->facebook ?></label>
                        <input type="text" name="facebook" class="form-control" value="<?= $settings->facebook ?>" />
                    </div>

                    <div class="form-group">
                        <label><i class="fab fa-twitter"></i> <?= $language->admin_website_settings->input->twitter ?></label>
                        <input type="text" name="twitter" class="form-control" value="<?= $settings->twitter ?>" />
                    </div>

                    <div class="form-group">
                        <label><i class="fab fa-instagram"></i> <?= $language->admin_website_settings->input->instagram ?></label>
                        <input type="text" name="instagram" class="form-control" value="<?= $settings->instagram ?>" />
                    </div>

                    <div class="form-group">
                        <label><i class="fab fa-youtube"></i> <?= $language->admin_website_settings->input->youtube ?></label>
                        <input type="text" name="youtube" class="form-control" value="<?= $settings->youtube ?>" />
                    </div>

                </div>


                <div class="tab-pane fade" id="sources">
                    <?php
                    if($plugins->exists_and_active('instagram')) {
                        require_once $plugins->require('instagram', 'views/admin/website_settings');
                    }
                    ?>

                    <?php
                    if($plugins->exists_and_active('facebook')) {
                        require_once $plugins->require('facebook', 'views/admin/website_settings');
                    }
                    ?>

                    <?php
                    if($plugins->exists_and_active('youtube')) {
                        require_once $plugins->require('youtube', 'views/admin/website_settings');
                    }
                    ?>

                    <?php
                    if($plugins->exists_and_active('twitter')) {
                        require_once $plugins->require('twitter', 'views/admin/website_settings');
                    }
                    ?>
                </div>

                <div class="tab-pane fade" id="cron">
                    <p class="text-muted"><?= $language->admin_website_settings->input->cron_help ?></p>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->cron_queries ?></label>
                        <input type="number" min="1" max="5" name="cron_queries" class="form-control" value="<?= $settings->cron_queries ?>" />
                        <small class="text-muted"><?= $language->admin_website_settings->input->cron_queries_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->cron_mode ?></label>

                        <select name="cron_mode" class="custom-select form-control">
                            <option value="ACTIVE" <?= ($settings->cron_mode == 'ACTIVE') ? 'selected' : null ?>>ACTIVE</option>
                            <option value="ALL" <?= ($settings->cron_mode == 'ALL') ? 'selected' : null ?>>ALL</option>
                        </select>

                        <small class="text-muted"><?= $language->admin_website_settings->input->cron_mode_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->cron_auto_add_missing_logs ?></label>
                        <select class="custom-select" name="cron_auto_add_missing_logs">
                            <option value="1" <?php if($settings->cron_auto_add_missing_logs) echo 'selected' ?>><?= $language->global->yes ?></option>
                            <option value="0" <?php if(!$settings->cron_auto_add_missing_logs) echo 'selected' ?>><?= $language->global->no ?></option>
                        </select>
                        <small class="text-muted"><?= $language->admin_website_settings->input->cron_auto_add_missing_logs_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->cron_url ?></label>
                        <input type="text" disabled="disabled" class="form-control" value="<?= $settings->url . 'cron' ?>" />
                        <small class="text-muted"><?= $language->admin_website_settings->input->cron_url_help ?></small>
                    </div>

                    <hr class="mt-4 mb-4" />

                    <h5>Info</h5>
                    <p>If you are going to set up the cron to run <strong>once every minute</strong> then you will be able to query about <strong><span id="queries_per_day"><?= $settings->cron_queries * 1440 ?></span> queries per day</strong>. Change the Cron Queries if you want to get other approximations.</p>
                </div>

                <div class="tab-pane fade" id="email">

                    <h5>SMTP</h5>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->smtp_host ?></label>
                        <input type="text" name="smtp_host" class="form-control" value="<?= $settings->smtp_host ?>" />
                        <small class="form-text text-muted"><?= $language->admin_website_settings->input->smtp_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->smtp_from ?></label>
                        <input type="text" name="smtp_from" class="form-control" value="<?= $settings->smtp_from ?>" />
                        <small class="form-text text-muted"><?= $language->admin_website_settings->input->smtp_from_help ?></small>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label><?= $language->admin_website_settings->input->smtp_encryption ?></label>
                                <select name="smtp_encryption" class="custom-select form-control">
                                    <option value="0" <?= ($settings->smtp_encryption == '0') ? 'selected' : null ?>>None</option>
                                    <option value="ssl" <?= ($settings->smtp_encryption == 'ssl') ? 'selected' : null ?>>SSL</option>
                                    <option value="tls" <?= ($settings->smtp_encryption == 'tls') ? 'selected' : null ?>>TLS</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <div class="form-group">
                                <label><?= $language->admin_website_settings->input->smtp_port ?></label>
                                <input type="text" name="smtp_port" class="form-control" value="<?= $settings->smtp_port ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" name="smtp_auth" type="checkbox" value="" <?= ($settings->smtp_auth) ? 'checked' : null ?>>
                            <?= $language->admin_website_settings->input->smtp_auth ?>
                        </label>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->smtp_user ?></label>
                        <input type="text" name="smtp_user" class="form-control" value="<?= $settings->smtp_user ?>" <?= ($settings->smtp_auth) ? null : 'disabled' ?>/>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->smtp_pass ?></label>
                        <input type="text" name="smtp_pass" class="form-control" value="<?= $settings->smtp_pass ?>" <?= ($settings->smtp_auth) ? null : 'disabled' ?>/>
                    </div>

                    <div class="">
                        <a href="admin/website-settings/test-email/<?= Security::csrf_get_session_token('url_token') ?>" class="btn btn-info"><?= $language->admin_website_settings->button->test_email ?></a>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->button->test_email_help ?></small>
                    </div>

                    <hr />

                    <h5>Reports</h5>
                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->email_reports ?></label>

                        <select class="custom-select" name="email_reports">
                            <option value="1" <?php if($settings->email_reports) echo 'selected' ?>><?= $language->global->yes ?></option>
                            <option value="0" <?php if(!$settings->email_reports) echo 'selected' ?>><?= $language->global->no ?></option>
                        </select>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->input->email_reports_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->email_reports_default ?></label>

                        <select class="custom-select" name="email_reports_default">
                            <option value="1" <?php if($settings->email_reports_default) echo 'selected' ?>><?= $language->global->yes ?></option>
                            <option value="0" <?php if(!$settings->email_reports_default) echo 'selected' ?>><?= $language->global->no ?></option>
                        </select>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->input->email_reports_default_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->email_reports_favorites ?></label>

                        <select class="custom-select" name="email_reports_favorites">
                            <option value="1" <?php if($settings->email_reports_favorites) echo 'selected' ?>><?= $language->global->yes ?></option>
                            <option value="0" <?php if(!$settings->email_reports_favorites) echo 'selected' ?>><?= $language->global->no ?></option>
                        </select>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->input->email_reports_favorites_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->email_reports_frequency ?></label>

                        <select class="custom-select" name="email_reports_frequency">
                            <option value="DAILY" <?php if($settings->email_reports_frequency == 'DAILY') echo 'selected' ?>><?= $language->global->date->daily ?></option>
                            <option value="WEEKLY" <?php if($settings->email_reports_frequency == 'WEEKLY') echo 'selected' ?>><?= $language->global->date->weekly ?></option>
                            <option value="MONTHLY" <?php if($settings->email_reports_frequency == 'MONTHLY') echo 'selected' ?>><?= $language->global->date->monthly ?></option>
                        </select>
                    </div>

                    <hr />

                    <h5>Other</h5>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->email_confirmation ?></label>

                        <select class="custom-select" name="email_confirmation">
                            <option value="1" <?php if($settings->email_confirmation) echo 'selected' ?>><?= $language->global->yes ?></option>
                            <option value="0" <?php if(!$settings->email_confirmation) echo 'selected' ?>><?= $language->global->no ?></option>
                        </select>
                    </div>
                </div>


                <div class="tab-pane fade" id="email_templates">

                    <h5><?= $language->admin_website_settings->input->activation_email_template ?></h5>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->email_template_subject ?></label>
                        <input type="text" class="form-control" name="activation_email_subject" value="<?= $settings->activation_email_template_subject ?>" />

                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->name ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->website_title ?></small>

                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->email_template_body ?></label>
                        <textarea class="form-control" name="activation_email_body" rows="5"><?= $settings->activation_email_template_body ?></textarea>

                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->help ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->name ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->website_title ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->activation_link ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->account_username ?></small>

                    </div>

                    <hr class="my-4"/>

                    <h5><?= $language->admin_website_settings->input->credentials_email_template ?></h5>
                    <small class="form-text text-muted"><?= $language->admin_website_settings->input->credentials_email_template_help ?></small>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->email_template_subject ?></label>
                        <input type="text" class="form-control" name="credentials_email_subject" value="<?= $settings->credentials_email_template_subject ?>" />

                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->name ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->website_title ?></small>

                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->email_template_body ?></label>
                        <textarea class="form-control" name="credentials_email_body" rows="5"><?= $settings->credentials_email_template_body ?></textarea>

                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->help ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->name ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->website_title ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->website_link ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->account_username ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->account_password ?></small>

                    </div>

                    <hr class="my-4"/>

                    <h5><?= $language->admin_website_settings->input->lost_password_email_template ?></h5>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->email_template_subject ?></label>
                        <input type="text" class="form-control" name="lost_password_email_subject" value="<?= $settings->lost_password_email_template_subject ?>" />

                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->name ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->website_title ?></small>

                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->email_template_body ?></label>
                        <textarea class="form-control" name="lost_password_email_body" rows="5"><?= $settings->lost_password_email_template_body ?></textarea>

                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->help ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->name ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->website_title ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->lost_password_link ?></small>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->email_template_keys->account_username ?></small>

                    </div>

                </div>


                <div class="tab-pane fade" id="email_notifications">

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->admin_email_notification_emails ?></label>
                        <textarea class="form-control" name="admin_email_notification_emails" rows="5"><?= $settings->admin_email_notification_emails ?></textarea>
                        <small class="form-text text-muted"><?= $language->admin_website_settings->input->admin_email_notification_emails_help ?></small>
                    </div>

                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input"  name="admin_new_user_email_notification" <?php if($settings->admin_new_user_email_notification) echo 'checked' ?>>
                            <?= $language->admin_website_settings->input->admin_new_user_email_notification ?>
                        </label>

                        <small class="form-text text-muted"><?= $language->admin_website_settings->input->admin_new_user_email_notification_help ?></small>
                    </div>

                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input"  name="admin_new_payment_email_notification" <?php if($settings->admin_new_payment_email_notification) echo 'checked' ?>>
                            <?= $language->admin_website_settings->input->admin_new_payment_email_notification ?>
                        </label>

                        <small class="form-text text-muted"><?= $language->admin_website_settings->input->admin_new_payment_email_notification_help ?></small>
                    </div>

                </div>

                <div class="tab-pane fade" id="proxy">
                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->proxy ?></label>

                        <select class="custom-select" name="proxy">
                            <option value="1" <?php if($settings->proxy) echo 'selected' ?>><?= $language->global->yes ?></option>
                            <option value="0" <?php if(!$settings->proxy) echo 'selected' ?>><?= $language->global->no ?></option>
                        </select>
                        <small class="text-muted"><?= $language->admin_website_settings->input->proxy_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->proxy_exclusive ?></label>

                        <select class="custom-select" name="proxy_exclusive">
                            <option value="1" <?php if($settings->proxy_exclusive) echo 'selected' ?>><?= $language->global->yes ?></option>
                            <option value="0" <?php if(!$settings->proxy_exclusive) echo 'selected' ?>><?= $language->global->no ?></option>
                        </select>
                        <small class="text-muted"><?= $language->admin_website_settings->input->proxy_exclusive_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->proxy_timeout ?></label>
                        <input type="number" name="proxy_timeout" class="form-control" value="<?= $settings->proxy_timeout ?>" />
                        <small class="text-muted"><?= $language->admin_website_settings->input->proxy_timeout_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->proxy_failed_requests_pause ?></label>
                        <input type="number" name="proxy_failed_requests_pause" class="form-control" value="<?= $settings->proxy_failed_requests_pause ?>" />
                        <small class="text-muted"><?= $language->admin_website_settings->input->proxy_failed_requests_pause_help ?></small>
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_website_settings->input->proxy_pause_duration ?></label>
                        <input type="number" name="proxy_pause_duration" class="form-control" value="<?= $settings->proxy_pause_duration ?>" />
                        <small class="text-muted"><?= $language->admin_website_settings->input->proxy_pause_duration_help ?></small>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" name="submit" class="btn btn-primary"><?= $language->global->submit_button ?></button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
    $(document).ready(() => {

        $('input[name="cron_queries"]').on('keyup keypress blur change', (event) => {
            $('#queries_per_day').html(parseInt($(event.currentTarget).val()) * 1440);
        })

        $('input[name="smtp_auth"]').on('change', (event) => {

            if($(event.currentTarget).is(':checked')) {
                $('input[name="smtp_user"],input[name="smtp_pass"]').removeAttr('disabled');
            } else {
                $('input[name="smtp_user"],input[name="smtp_pass"]').attr('disabled', 'true');
            }

        })
    })
</script>
