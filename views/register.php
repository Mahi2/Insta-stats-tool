<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-center">
	<div class="card card-shadow animated fadeIn col-xs-12 col-sm-10 col-md-6 col-lg-4">
		<div class="card-body">

			<h4 class="card-title"><?= $language->register->header ?></h4>
            <small><a href="login" class="text-muted" role="button"><?= $language->register->subheader ?></a></small>

			<form action="register" method="post" role="form">
				<div class="form-group mt-5">
					<input type="text" name="username" class="form-control form-control-border" value="<?= $register_username ?>" placeholder="<?= $language->register->input->username ?>" aria-label="<?= $language->register->input->username ?>" required="required" />
				</div>

				<div class="form-group mt-5">
					<input type="text" name="name" class="form-control form-control-border" value="<?= $register_name ?>" placeholder="<?= $language->register->input->name ?>" aria-label="<?= $language->register->input->name ?>" required="required" />
				</div>

				<div class="form-group mt-5">
					<input type="text" name="email" class="form-control form-control-border" value="<?= $register_email ?>" placeholder="<?= $language->register->input->email ?>" aria-label="<?= $language->register->input->email ?>" required="required" />
				</div>

				<div class="form-group mt-5">
					<input type="password" name="password" class="form-control form-control-border" placeholder="<?= $language->register->input->password ?>" aria-label="<?= $language->register->input->password ?>" required="required" />
				</div>

				<div class="form-group mt-5">
					  <?php $captcha->display() ?>
				</div>

				<div class="form-group mt-5">
					<button type="submit" name="submit" class="btn btn-default btn-block"><?= $language->global->submit_button ?></button>
				</div>

			</form>
		</div>
	</div>
</div>