<?php
defined('MAHIDCODE') || die();

if($settings->directory == 'DISABLED') redirect();

$error = [];

if(!isset($_POST['global_form_token']) || isset($_POST['global_form_token']) && !Security::csrf_check_session_token('global_form_token', $_POST['global_form_token'])) {
    $error[] = $language->global->error_message->invalid_token;
}

/* Response */
if(!empty($error)) {
    Response::json($error, 'error');
    die();
}

/* Get the reports */
$start = (int) filter_var($_POST['start'], FILTER_SANITIZE_NUMBER_INT);
$real_limit = (int) filter_var($_POST['limit'], FILTER_SANITIZE_NUMBER_INT);
$limit = $real_limit + 1;

/* Parse the filters */
$where = '`is_private` = 0 ';
$order_by_column = '`id`';
$order_by_criteria = 'ASC';

$bio = false;
$followers_from = $followers_to = false;
$engagement_from = $engagement_to = false;
$order_by_filter = $order_by_type =  false;


if(isset($_POST['filters'])) {
    /* Trim to makesure */
    foreach($_POST['filters'] AS $key => $value) {
        $_POST['filters'][$key] = trim($_POST['filters'][$key]);
    }

    if(isset($_POST['filters']['order_by_filter']) && !empty($_POST['filters']['order_by_filter'])) {

        $order_by_filter = Database::clean_string($_POST['filters']['order_by_filter']);
        $order_by_filter = in_array($order_by_filter, ['id', 'username', 'followers', 'following', 'uploads', 'average_engagement_rate']) ? $order_by_filter : 'ASC';

        $order_by_column = '`' . $order_by_filter . '` ';

    }

    if(isset($_POST['filters']['order_by_type']) && !empty($_POST['filters']['order_by_type'])) {

        $order_by_type = strtoupper(Database::clean_string($_POST['filters']['order_by_type']));
        $order_by_type = in_array($order_by_type, ['ASC', 'DESC']) ? $order_by_type : 'ASC';

        $order_by_criteria = $order_by_type;

    }

    if(isset($_POST['filters']['bio_filter']) && !empty($_POST['filters']['bio_filter'])) {

        $bio = Database::clean_string($_POST['filters']['bio_filter']);

        $where .= 'AND `description` LIKE \'%' . $bio . '%\'';

    }

    if(isset($_POST['filters']['followers_from_filter']) && !empty($_POST['filters']['followers_from_filter'])) {

        $followers_from = (int) Database::clean_string($_POST['filters']['followers_from_filter']);

        $where .= 'AND `followers` >= ' . $followers_from . ' ';

    }

    if(isset($_POST['filters']['followers_to_filter']) && !empty($_POST['filters']['followers_to_filter'])) {

        $followers_to = (int) Database::clean_string($_POST['filters']['followers_to_filter']);

        $comparison_sign = ($followers_to >= 1000000) ? '>=' : '<=';

        $where .= 'AND `followers` '. $comparison_sign . ' ' . $followers_to . ' ';

    }

    if(isset($_POST['filters']['engagement_from_filter']) && !empty($_POST['filters']['engagement_from_filter'])) {

        $engagement_from = (float) Database::clean_string($_POST['filters']['engagement_from_filter']);

        $where .= 'AND `average_engagement_rate` >= ' . $engagement_from . ' ';

    }

    if(isset($_POST['filters']['engagement_to_filter']) && !empty($_POST['filters']['engagement_to_filter'])) {

        $engagement_to = (float) Database::clean_string($_POST['filters']['engagement_to_filter']);

        $comparison_sign = ($engagement_to >= 10) ? '>=' : '<=';

        $where .= 'AND `average_engagement_rate` '. $comparison_sign . ' ' . $engagement_to . ' ';

    }
}

$reports_result = $database->query("SELECT * FROM `instagram_users` WHERE {$where} ORDER BY {$order_by_column} {$order_by_criteria} LIMIT {$start}, {$limit}");
$total_results = $reports_result->num_rows;

/* Build html for active filters */
$active_filters_html = '';

if($order_by_filter) {
    $active_filters_html .= '
        <a href="#" class="badge badge-light badge-pill" id="order_by_filter_remove"><i class="fas fa-neuter"></i> ' . $language->directory->display->order_by_filter . ': ' . $order_by_filter . ' <i class="fas fa-times"></i></a>
    ';
}

if($order_by_type) {
    $active_filters_html .= '
        <a href="#" class="badge badge-light badge-pill" id="order_by_type_remove"><i class="fas fa-sort"></i> ' . $language->directory->display->order_by_type . ': ' . $order_by_type . ' <i class="fas fa-times"></i></a>
    ';
}

if($bio) {
    $active_filters_html .= '
        <a href="#" class="badge badge-light badge-pill" id="bio_filter_remove"><i class="fas fa-pencil-alt"></i> ' . $language->directory->display->bio . ': ' . $bio . ' <i class="fas fa-times"></i></a>
    ';
}

if($followers_from) {
    $active_filters_html .= '
        <a href="#" class="badge badge-light badge-pill" id="followers_from_filter_remove"><i class="fas fa-users"></i> ' . $language->directory->display->followers_from . ': ' . $followers_from . ' <i class="fas fa-times"></i></a>
    ';
}

if($followers_to) {
    $active_filters_html .= '
        <a href="#" class="badge badge-light badge-pill" id="followers_to_filter_remove"><i class="fas fa-users"></i> ' . $language->directory->display->followers_to . ': ' . $followers_to . ' <i class="fas fa-times"></i></a>
    ';
}

if($engagement_from) {
    $active_filters_html .= '
        <a href="#" class="badge badge-light badge-pill" id="engagement_from_filter_remove"><i class="fas fa-comments"></i> ' . $language->directory->display->engagement_from . ': ' . $engagement_from . ' <i class="fas fa-times"></i></a>
    ';
}

if($engagement_to) {
    $active_filters_html .= '
        <a href="#" class="badge badge-light badge-pill" id="engagement_to_filter_remove"><i class="fas fa-comments"></i> ' . $language->directory->display->engagement_to . ': ' . $engagement_to . ' <i class="fas fa-times"></i></a>
    ';
}
$order_by_filter = in_array($order_by_filter, ['id', 'username', 'followers', 'following', 'uploads', 'average_engagement_rate']) ? $order_by_filter : 'ASC';

/* Build html for the filters */
$filters_html = '
<div class="d-flex justify-content-between">
    <div id="active_filters"></div>

    <div class="btn-group">
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-table"></i> ' . $language->directory->display->order_by . '</button>
            <div class="dropdown-menu px-3 py-2" data-no-toggle>
                <h6 class="dropdown-header">' . $language->directory->display->order_by_help . '</h6>

                <div class="form-inline d-flex justify-content-between">
                    <div class="form-group">
                        <select class="form-control" name="order_by_filter">
                            <option value="">' . $language->directory->display->order_by_filter . '</option>
                            <option value="id" selected="true">ID</option>
                            <option value="username">' . $language->instagram->report->display->username . '</option>
                            <option value="followers">' . $language->instagram->report->display->followers . '</option>
                            <option value="following">' . $language->instagram->report->display->following . '</option>
                            <option value="uploads">' . $language->instagram->report->display->uploads . '</option>
                            <option value="average_engagement_rate">' . $language->instagram->report->display->average_engagement_rate . '</option>
                        </select>
                    </div>
                    
                    <div class="form-group mt-2">
                        <select class="form-control" name="order_by_type">
                            <option value="">' . $language->directory->display->order_by_type . '</option>
                            <option value="ASC" selected="true">' . $language->directory->display->ascending . '</option>
                            <option value="DESC">' . $language->directory->display->descending . '</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fas fa-pencil-alt"></i> ' . $language->directory->display->bio . '</button>
            <div class="dropdown-menu" data-no-toggle>
                <div class="px-3 py-2">
                    <h6 class="dropdown-header">' . $language->directory->display->bio_help . '</h6>
                    
                    <div class="form-group">
                        <input type="text" name="bio_filter" class="form-control form-control-border" placeholder="' . $language->directory->input->bio_filter . '" value="'. $bio . '" />
                    </div>
                </div>
            </div>
        </div>
        
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-users"></i> ' . $language->directory->display->followers . '</button>
            <div class="dropdown-menu px-3 py-2" data-no-toggle>
                <h6 class="dropdown-header">' . $language->directory->display->followers_help . '</h6>
                
                <div class="form-inline d-flex justify-content-between">
                    <div class="form-group">
                        <select class="form-control" name="followers_from_filter">
                            <option value="">' . $language->directory->display->from . '</option>
                            <option value="500">500</option>
                            <option value="5000">5K</option>
                            <option value="10000">10K</option>
                            <option value="25000">25K</option>
                            <option value="50000">50K</option>
                            <option value="100000">100K</option>
                            <option value="250000">250K</option>
                            <option value="500000">500K</option>
                            <option value="1000000">1M</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <select class="form-control" name="followers_to_filter">
                            <option value="">' . $language->directory->display->to . '</option>
                            <option value="1000">1K</option>
                            <option value="5000">5K</option>
                            <option value="10000">10K</option>
                            <option value="25000">25K</option>
                            <option value="50000">50K</option>
                            <option value="100000">100K</option>
                            <option value="250000">250K</option>
                            <option value="500000">500K</option>
                            <option value="1000000">>1M</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-comments"></i> ' . $language->directory->display->engagement . '</button>
            <div class="dropdown-menu px-3 py-2" data-no-toggle>
                <h6 class="dropdown-header">' . $language->directory->display->engagement_help . '</h6>
                
                <div class="form-inline d-flex justify-content-between">
                    <div class="form-group">
                        <select class="form-control" name="engagement_from_filter">
                            <option value="">' . $language->directory->display->from . '</option>
                            <option value="0.5">0.5%</option>
                            <option value="1">1%</option>
                            <option value="2">2%</option>
                            <option value="3">3%</option>
                            <option value="5">5%</option>
                            <option value="7">7%</option>
                            <option value="10">10%</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <select class="form-control" name="engagement_to_filter">
                            <option value="">' . $language->directory->display->to . '</option>
                            <option value="1">1%</option>
                            <option value="2">2%</option>
                            <option value="3">3%</option>
                            <option value="5">5%</option>
                            <option value="7">7%</option>
                            <option value="10">>10%</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
      
    </div>
</div>';

/* Start building the html */
$html = '';

/* Counter for the limit so that we dont go over the real limit */
$limit_counter = 0;

$html .= '<div class="result">';

while($source_account = $reports_result->fetch_object()):

    if($limit_counter >= $real_limit) {
        continue;
    }

    if($bio) {
        $source_account->description = str_ireplace($bio, '<strong>' . $bio . '</strong>', $source_account->description);
    }

    $html .= ' 
    <div class="card card-shadow mt-4 mb-1 index-card">
        <div class="card-body card-body pt-4 pb-2">
            <div class="d-flex flex-column flex-sm-row flex-wrap">';

    $html .= '<div class="col-sm-4 col-md-3 col-lg-2 d-flex justify-content-center justify-content-sm-start">';

    if(!empty($source_account->profile_picture_url)):
        $html .= '<img src="' . $source_account->profile_picture_url . '" onerror="$(this).attr(\'src\', ($(this).data(\'failover\')))"  data-failover="' . $settings->url . ASSETS_ROUTE . 'images/default_avatar.png" class="img-responsive rounded-circle directory-instagram-avatar" alt="' . $source_account->full_name . '" />';
    endif;

    $html .= '</div>';

    $html .= '
                <div class="col-sm-8 col-md-9 col-lg-5 d-flex justify-content-center justify-content-sm-start">
                    <div class="row d-flex flex-column">
                        <p class="m-0">
                            <a href="https://instagram.com/'.$source_account->username . '" target="_blank" class="text-dark" rel="nofollow">@' . $source_account->username . '</a>
                        </p>

                        <h5>
                            <a class="text-dark" href="report/' . $source_account->username . '">' . $source_account->full_name . '</a> ';

    if($source_account->is_private):
        $html .= '<span data-toggle="tooltip" title="' . $language->instagram->report->display->private . '"><i class="fa fa-lock user-private-badge"></i></span>';
    endif;

    if($source_account->is_verified):
        $html .= '<span data-toggle="tooltip" title="' . $language->instagram->report->display->verified . '"><i class="fa fa-check-circle user-verified-badge"></i></span>';
    endif;

    $html .= '</h5>';

    if($bio) {
        $html .= '<small class="text-muted">' . $source_account->description . '</small>';
    }

    $html .= '
                    </div>  
                </div>';

    $html .= '
                <div class="col-md-12 col-lg-5 d-flex justify-content-around align-items-center mt-4 mt-lg-0">
                    <div class="col d-flex flex-column justify-content-center">
                        <strong>' . $language->instagram->report->display->followers . '</strong>
                        <p class="directory-header-number">' . nr($source_account->followers) . '</p>
                    </div>

                    <div class="col d-flex flex-column justify-content-center">
                        <strong>' . $language->instagram->report->display->uploads . '</strong>
                        <p class="directory-header-number">' . nr($source_account->uploads) .'</p>
                    </div>

                    <div class="col d-flex flex-column justify-content-center">
                        <strong>' . $language->instagram->report->display->engagement_rate . '</strong>
                        <p class="directory-header-number">
                            ' . (($source_account->is_private || !is_numeric($source_account->average_engagement_rate)) ? 'N/A' : nr($source_account->average_engagement_rate, 2)) . '%                               
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>';

    $limit_counter++;
endwhile;

if($total_results > $real_limit) {
    $html .= '
    <div class="text-center">
        <button type="submit" name="submit" class="btn btn-dark mt-5" id="show_more">' . $language->global->show_more . '</button>
    </div>';
}

$html .= '</div>';

/* When there is no result show a notice */
if($total_results == 0) {
    $html .= '<div class="result">';

    $html .= '
                <div class="alert alert-info animated fadeIn mt-5">
                    <strong>' . $language->directory->info_message->no_results_title . '</strong> ' . $language->directory->info_message->no_results . '
                </div>';

    $html .= '</div>';
}

Response::json('', 'success', [
    'html' => $html,
    'filters_html' => $filters_html,
    'active_filters_html' => $active_filters_html,
    'has_more' => (bool) ($total_results > $real_limit)
]);

$controller_has_view = false;
