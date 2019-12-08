<?php defined('ALTUMCODE') || die() ?>

<div class="card card-shadow mb-3">
    <div class="card-body">

        <h4><?= $language->admin_pages_management->header ?></h4>

        <form action="" method="post" role="form">
            <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />

            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label><?= $language->admin_pages_management->input->title ?></label>
                        <input type="text" name="title" class="form-control" value="" />
                    </div>
                </div>

                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label><?= $language->admin_pages_management->input->position ?></label>
                        <select class="form-control" name="position">
                            <option value="1"><?= $language->admin_pages_management->input->position_top ?></option>
                            <option value="0"><?= $language->admin_pages_management->input->position_bottom ?></option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-12 col-md-12">
                    <div class="form-group">
                        <label><?= $language->admin_pages_management->input->url ?></label>
                        <input type="text" name="url" class="form-control" value="" />
                        <small class="text-muted"><?= $language->admin_pages_management->input->url_help ?></small>
                    </div>
                </div>


                <div class="col-12">
                    <div class="form-group">
                        <label><?= $language->admin_pages_management->input->description ?></label>
                        <textarea id="description" name="description" class="form-control"></textarea>
                    </div>
                </div>

            </div>

            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary"><?= $language->global->submit_button ?></button>
            </div>
        </form>

    </div>
</div>


<div class="card card-shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-inverse">
                <tr>
                    <th><?= $language->admin_pages_management->table->title ?></th>
                    <th><?= $language->admin_pages_management->table->url ?></th>
                    <th><?= $language->admin_pages_management->table->position ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php while($page_data = $pages_result->fetch_object()): ?>

                    <tr>
                        <td><?= $page_data->title ?></td>
                        <td><?= $page_data->url ?></td>
                        <td><?= ($page_data->position == '0') ? $language->admin_pages_management->table->position_bottom : $language->admin_pages_management->table->position_top ?></td>
                        <td><?= User::admin_generate_buttons('page', $page_data->page_id) ?></td>
                    </tr>

                <?php endwhile ?>
                </tbody>
            </table>
        </div>
    </div>
</div>