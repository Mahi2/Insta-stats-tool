<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-center">
	<div class="card card-shadow animated fadeIn col-xs-12 col-sm-10 col-md-6 col-lg-4">
		<div class="card-body">

			<h4 class="card-title"><?= $language->login->header ?></h4>
            <small><a href="lost-password" class="text-muted" role="button"><?= $language->login->button->lost_password ?></a> / <a href="resend-activation" class="text-muted" role="button"><?= $language->login->button->resend_activation ?></a></small>

			<form action="" method="post" role="form">
				<div class="form-group mt-5">
					<input type="text" name="username" class="form-control form-control-border" placeholder="<?= $language->login->input->username ?>" value="<?= $login_username ?>" aria-label="<?= $language->login->input->username ?>" required="required" />
				</div>

				<div class="form-group mt-5">
					<input type="password" name="password" class="form-control form-control-border" placeholder="<?= $language->login->input->password ?>" aria-label="<?= $language->login->input->password ?>" required="required" />
				</div>

				<div class="form-check mt-5">
					<label class="form-check-label">
						<input type="checkbox" class="form-check-input" name="rememberme">
						<?= $language->login->input->remember_me ?>
					</label>
				</div>


				<div class="form-group mt-5">
					<button type="submit" name="submit" class="btn btn-default btn-block my-1"><?= $language->login->button->login ?></button>
				</div>

                <div class="row">
                    <?php if($settings->facebook_login): ?>
                    <div class="col-sm mt-1">
                        <a href="<?= $facebook_login_url ?>" class="btn btn-primary btn-block"><?= sprintf($language->login->button->facebook, "<i class=\"fab fa-facebook fa-lg\"></i>") ?></a>
                    </div>
                    <?php endif ?>

                    <?php if($settings->instagram_login): ?>
                    <div class="col-sm mt-1">
                        <a href="<?= $instagram_login_url ?>" class="btn btn-primary bg-instagram btn-block"><?= sprintf($language->login->button->instagram, "<i class=\"fab fa-instagram fa-lg\"></i>") ?></a>
                    </div>
                    <?php endif ?>
                </div>

			</form>
		</div>
	</div>
</div>