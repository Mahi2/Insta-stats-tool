<?php defined('ALTUMCODE') || die() ?>

<div class="card card-shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table id="results" class="table">
                <thead class="thead-black">
                    <tr>
                        <th><?= $language->admin_users_management->table->username ?></th>
                        <th><?= $language->admin_users_management->table->name ?></th>
                        <th><?= $language->admin_users_management->table->email ?></th>
                        <th><?= $language->admin_users_management->table->active ?></th>
                        <th><?= $language->admin_users_management->table->registration_date ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<input type="hidden" name="url" value="<?= $settings->url . $route . 'users_management_ajax' ?>" />

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
                data: 'username',
                searchable: true,
                sortable: true
            },
            {
                data: 'name',
                searchable: true,
                sortable: true
            },
            {
                data: 'email',
                searchable: true,
                sortable: true
            },
            {
                data: 'active',
                searchable: false,
                sortable: true
            },
            {
                data: 'date',
                searchable: false,
                sortable: true
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