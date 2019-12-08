<?php defined('ALTUMCODE') || die() ?>

<div class="card card-shadow">
    <div class="card-body">

        <h4 class="d-flex justify-content-between">
            <div class="d-flex">
                <span class="mr-3"><?= $language->admin_page_edit->header ?></span>

                <?= User::admin_generate_buttons('page', $page_id) ?>
            </div>

            <div><?= User::generate_go_back_button('admin/pages-management') ?></div>
        </h4>

        <form action="" method="post" role="form">
            <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />

            <div class="form-group">
                <label><?= $language->admin_page_edit->input->title ?></label>
                <input type="text" name="title" class="form-control" value="<?= $page->title ?>" />
            </div>

            <div class="form-group">
                <label><?= $language->admin_page_edit->input->url ?></label>
                <input type="text" name="url" class="form-control" value="<?= $page->url ?>" />
            </div>

            <div class="form-group">
                <label><?= $language->admin_page_edit->input->description ?></label>
                <textarea id="description" name="description" class="form-control"><?= $page->description ?></textarea>
            </div>

            <div class="form-group">
                <label><?= $language->admin_page_edit->input->position ?></label>
                <select class="form-control" name="position">
                    <option value="1" <?php if($page->position == '1') echo 'selected="true"' ?>><?= $language->admin_page_edit->input->position_top ?></option>
                    <option value="0" <?php if($page->position == '0') echo 'selected="true"' ?>><?= $language->admin_page_edit->input->position_bottom ?></option>
                </select>
            </div>

            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary mt-5"><?= $language->global->submit_button ?></button>
            </div>
        </form>

    </div>
</div>