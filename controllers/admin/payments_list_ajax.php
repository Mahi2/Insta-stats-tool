<?php
defined('ALTUMCODE') || die();
User::check_permission(1);

Security::csrf_page_protection_check('dynamic', false);

$datatable = new DataTable();
$datatable->set_accepted_columns(['username', 'type', 'email', 'name', 'amount', 'date']);
$datatable->process($_POST);

$result = $database->query("
    SELECT 
		`payments` . *, `users` . `username`, `users` . `user_id`, `users` . `type` AS `user_type`,
		(SELECT COUNT(*) FROM `payments`) AS `total_before_filter`,
		(SELECT COUNT(*) FROM `payments` LEFT JOIN `users` ON `payments` . `user_id` = `users` . `user_id` 	WHERE  `users` . `username` LIKE '%{$datatable->get_search()}%'  OR `users` . `name` LIKE '%{$datatable->get_search()}%' OR `payments` . `name` LIKE '%{$datatable->get_search()}%' OR `users` . `email` LIKE '%{$datatable->get_search()}%' OR `payments` . `email` LIKE '%{$datatable->get_search()}%') AS `total_after_filter`
	FROM 
        `payments`
	LEFT JOIN
		`users` ON `payments` . `user_id` = `users` . `user_id`
	WHERE 
		`users` . `username` LIKE '%{$datatable->get_search()}%' 
		OR `users` . `name` LIKE '%{$datatable->get_search()}%'
        OR `payments` . `name` LIKE '%{$datatable->get_search()}%'
		OR `users` . `email` LIKE '%{$datatable->get_search()}%'
        OR `payments` . `email` LIKE '%{$datatable->get_search()}%'
	ORDER BY 
        " . $datatable->get_order() . "
	LIMIT
		{$datatable->get_start()}, {$datatable->get_length()}	
");

$total_before_filter = 0;
$total_after_filter = 0;

$data = [];

while($entry = $result->fetch_object()):

    $username_extra = $entry->user_type > 0 ? ' <span class="text-muted" data-toggle="tooltip" title="' . $language->admin_users_management->tooltip->admin .'"><i class="fa fa-bookmark fa-sm"></i></span>' : '';
    $entry->username = '<a href="' . $settings->url . 'admin/user-edit/' . $entry->user_id . '"> ' . $entry->username . '</a>' . $username_extra;
    $entry->date = '<span data-toggle="tooltip" title="' . $entry->date . '">' . (new DateTime($entry->date))->format($language->global->date->datetime_format) . '</span>';
    $entry->amount = '<span class="text-success">' .  $entry->amount . '</span> ' . $entry->currency;

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