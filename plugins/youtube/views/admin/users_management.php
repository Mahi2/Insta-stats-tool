<?php defined('ALTUMCODE') || die() ?>

<div class="card card-shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table id="results" class="table">
                <thead class="thead-black">
                    <tr>
                        <th><?= $language->youtube->admin_users_management->table->youtube_id ?></th>
                        <th><?= $language->youtube->admin_users_management->table->title ?></th>
                        <th><?= $language->youtube->admin_users_management->table->last_check_date ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<input type="hidden" name="url" value="<?= $settings->url . $route . 'source-users-management/youtube/ajax' ?>" />

<script>
$(document).ready(() => {
    let datatable = $('#results').DataTable({
        language: <?= json_encode($language->datatable) ?>,
        serverSide: true,
        processing: true,
        ajax: {
            url: $('[name="url"]').val(),
            type: 'POST'
        },
        lengthMenu: [[25, 50, 100], [25, 50, 100]],
        columns: [
            {
                data: 'youtube_id',
                searchable: true,
                sortable: true
            },
            {
                data: 'title',
                searchable: true,
                sortable: true
            },
            {
                data: 'last_check_date',
                searchable: false,
                sortable: false
            },
            {
                data: 'actions',
                searchable: false,
                sortable: false
            }
        ],
        responsive: true,
        drawCallback: () => {
            $('[data-toggle="tooltip"]').tooltip();
        },
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5 text-muted'i><'col-sm-12 col-md-7'p>>"
    });

});
</script>
