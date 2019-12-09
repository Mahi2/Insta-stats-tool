<?php
defined('ALTUMCODE') || die();
User::check_permission(1);

Security::csrf_page_protection_check('dynamic', false);

$datatable = new DataTable();
$datatable->set_accepted_columns(['id', 'youtube_id', 'username', 'title', 'is_private', 'is_demo', 'last_check_date']);
$datatable->process($_POST);

$result = $database->query("
	SELECT
		`id`, `youtube_id`, `title`, `is_private`, `is_demo`, `last_check_date`,
		(SELECT COUNT(*) FROM `youtube_users`) AS `total_before_filter`,
		(SELECT COUNT(*) FROM `youtube_users` WHERE `username` LIKE '%{$datatable->get_search()}%' OR `title` LIKE '%{$datatable->get_search()}%' OR `youtube_id` LIKE '%{$datatable->get_search()}%') AS `total_after_filter`
	FROM
		`youtube_users`
	WHERE
		`username` LIKE '%{$datatable->get_search()}%'
		OR `title` LIKE '%{$datatable->get_search()}%'
        OR `youtube_id` LIKE '%{$datatable->get_search()}%'
	ORDER BY
		" . $datatable->get_order() . "
	LIMIT
		{$datatable->get_start()}, {$datatable->get_length()}
");

$total_before_filter = 0;
$total_after_filter = 0;

$data = [];

while($entry = $result->fetch_object()) {
    $youtube_id_extra = '';

    if($entry->is_demo) {
        $youtube_id_extra .= '<span class="text-muted" data-toggle="tooltip" title="' . $language->youtube->admin_users_management->tooltip->demo . '"><i class="fa fa-adjust fa-sm"></i></span>&nbsp;';
    }

    $entry->youtube_id = '<a href="' . $settings->url . 'report/' . $entry->youtube_id . '/youtube"  target="_blank">' . $entry->youtube_id . '</a> ' . $youtube_id_extra;
    $entry->last_check_date = '<span data-toggle="tooltip" title="' . $entry->last_check_date . '">' . (new DateTime($entry->last_check_date))->format($language->global->date->datetime_format) . '</span>';
    $entry->actions = '
    <div class="dropdown">
        <a href="#" data-toggle="dropdown" class="text-secondary dropdown-toggle dropdown-toggle-simple">
            <i class="fas fa-ellipsis-v"></i>
            
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" data-confirm="' . $language->global->info_message->confirm_delete . '" href="admin/source-users-management/' . $source . '/delete/' . $entry->id . '/' . Security::csrf_get_session_token('url_token') . '"><i class="fa fa-times"></i> ' . $language->global->delete . '</a>
            </div>
        </a>
    </div>
    ';

    $data[] = $entry;
    $total_before_filter = $entry->total_before_filter;
    $total_after_filter = $entry->total_after_filter;
}


Response::simple_json([
    'data' => $data,
    'draw' => $datatable->get_draw(),
    'recordsTotal' => $total_before_filter,
    'recordsFiltered' =>  $total_after_filter
]);


$controller_has_view = false;
