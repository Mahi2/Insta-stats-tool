<?php defined('ALTUMCODE') || die() ?>

<div class="alert alert-info ">
    <strong>Info</strong> Please test your proxies properly and make sure they work most of the time, do NOT use the proxy feature if you don't know what you're doing!
</div>

<div class="card card-shadow mb-3">
    <div class="card-body">

        <h4><?= $language->admin_proxies_management->header ?></h4>

        <form action="" method="post" role="form">
            <input type="hidden" name="form_token" value="<?= Security::csrf_get_session_token('form_token') ?>" />

            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <small class="text-muted"><?= $language->admin_proxies_management->input->help ?></small>

                    <div class="form-group">
                        <label><?= $language->admin_proxies_management->input->address ?></label>
                        <input type="text" name="address" class="form-control" value="<?= $default_values['address'] ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_proxies_management->input->port ?></label>
                        <input type="text" name="port" class="form-control" value="<?= $default_values['port'] ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_proxies_management->input->note ?></label>
                        <input type="text" name="note" class="form-control" value="<?= $default_values['note'] ?>" />
                        <small class="text-muted"><?= $language->admin_proxies_management->input->note_help ?></small>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6">
                    <small class="text-muted"><?= $language->admin_proxies_management->input->auth_help ?></small>

                    <div class="form-group">
                        <label><?= $language->admin_proxies_management->input->username ?></label>
                        <input type="text" name="username" class="form-control" value="<?= $default_values['username'] ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_proxies_management->input->password ?></label>
                        <input type="text" name="password" class="form-control" value="<?= $default_values['password'] ?>" />
                    </div>

                    <div class="form-group">
                        <label><?= $language->admin_proxies_management->input->method ?></label>
                        <select name="method" class="custom-select form-control">
                            <option value="0">HTTP</option>
                            <option value="1">HTTP_1_0</option>
                            <option value="4">SOCKS4</option>
                            <option value="6">SOCKS4A</option>
                            <option value="5">SOCKS5</option>
                        </select>
                        <small class="text-muted"><?= $language->admin_proxies_management->input->method_help ?></small>
                    </div>

                </div>
            </div>



            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary"><?= $language->admin_proxies_management->button->submit ?></button>
            </div>

        </form>


    </div>
</div>


<?php if($proxies_result->num_rows): ?>
<div class="card card-shadow">
    <div class="card-body">
        <table class="table table-hover">
            <thead class="thead-inverse">
            <tr>
                <th><?= $language->admin_proxies_management->table->address ?></th>
                <th><i class="fa fa-check-circle text-success fa-sm"></i> <?= $language->admin_proxies_management->table->total_successful_requests ?></th>
                <th><i class="fa fa-times-circle text-danger fa-sm"></i> <?= $language->admin_proxies_management->table->total_failed_requests ?></th>
                <th><?= $language->admin_proxies_management->table->note ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody id="results">

            <?php while($proxy = $proxies_result->fetch_object()): ?>

                <tr>
                    <td>
                        <span class="text-muted">
                            <?php
                            switch($proxy->method) {
                                case '0': echo 'HTTP'; break;
                                case '1': echo 'HTTP_1_0'; break;
                                case '4': echo 'SOCKS4'; break;
                                case '6': echo 'SOCKS4A'; break;
                                case '5': echo 'SOCKS5'; break;
                            }
                            ?>
                        </span>
                        <a href="admin/proxy-edit/<?= $proxy->proxy_id ?>"><?= $proxy->address . ':' . $proxy->port ?></a>
                    </td>
                    <td><?= $proxy->total_successful_requests ?></td>
                    <td><?= $proxy->total_failed_requests ?></td>
                    <td><?= empty($proxy->note) ? '-' : string_resize($proxy->note, 16) ?></td>
                    <td><?= User::admin_generate_buttons('proxy', $proxy->proxy_id) ?></td>
                </tr>

            <?php endwhile ?>

            </tbody>
        </table>
    </div>
</div>
<?php endif ?>