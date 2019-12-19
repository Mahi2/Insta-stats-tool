<?php
defined('ALTUMCODE') || die();
User::check_permission(1);

Security::csrf_page_protection_check('dynamic', false);

$datatable = new DataTable();
$datatable->set_accepted_columns(['user_id', 'username', 'name', 'email', 'date', 'type', 'active']);
$datatable->process($_POST);

$result = $database->query("
	SELECT
		`user_id`, `username`, `name`, `email`, `date`, `type`, `active`,
		(SELECT COUNT(*) FROM `users`) AS `total_before_filter`,
		(SELECT COUNT(*) FROM `users` WHERE `username` LIKE '%{$datatable->get_search()}%' OR `name` LIKE '%{$datatable->get_search()}%' OR `email` LIKE '%{$datatable->get_search()}%') AS `total_after_filter`
	FROM
		`users`
	WHERE
		`username` LIKE '%{$datatable->get_search()}%'
		OR `name` LIKE '%{$datatable->get_search()}%'
		OR `email` LIKE '%{$datatable->get_search()}%'
	ORDER BY
	    `type` DESC,
		" . $datatable->get_order() . "
	LIMIT
		{$datatable->get_start()}, {$datatable->get_length()}
");

$total_before_filter = 0;
$total_after_filter = 0;

$data = [];

while($entry = $result->fetch_object()):

    $username_extra = $entry->type > 0 ? ' <span class="text-muted" data-toggle="tooltip" title="' . $language->admin_users_management->tooltip->admin .'"><i class="fa fa-bookmark fa-sm"></i></span>' : '';
    $entry->username = $entry->username . $username_extra;

    $entry->active = $entry->active ? '<span class="badge badge-pill badge-success">' . $language->admin_users_management->display->user_active . '</span>' : '<span class="badge badge-pill badge-warning">' . $language->admin_users_management->display->user_disabled . '</span>';
    $entry->date = '<span data-toggle="tooltip" title="' . $entry->date . '">' . (new DateTime($entry->date))->format($language->global->date->datetime_format) . '</span>';
    $entry->actions = User::admin_generate_buttons('user', $entry->user_id);

    $data[] = $entry;

    $total_before_filter = $entry->total_before_filter;
    $total_after_filter = $entry->total_after_filter;
endwhile;


Response::simple_json([
    'data' => $data,
    'draw' => $datatable->get_draw(),
    'recordsTotal' => $total_before_filter,
    'recordsFiltered' =>  $total_after_filter
]);


$controller_has_view = false;