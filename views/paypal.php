<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-center">
    <div class="card card-shadow animated fadeIn col-xs-12 col-sm-10 col-md-6 col-lg-4">
        <div class="card-body">

            <h4 class="d-flex justify-content-between">
                <?= $language->store->paypal->header ?>
                <small><?= User::generate_go_back_button('store') ?></small>
            </h4>

            <form action="<?= $settings->url ?>paypal" method="post" role="form">
                <div class="form-group mt-5">
                    <label><?= $language->store->paypal->amount ?></label>
                    <input class="form-control" type="number" name="amount" value="5" min="1" />
                    <small class="text-muted"><?= sprintf($language->store->display->info, $settings->store_currency) ?></small>
                </div>

                <div class="form-group text-center mt-5">
                    <input type="image" class="paypal-submit" src="https://checkout.paypal.com/pwpp/1.6.3/images/pay-with-paypal.png" name="submit" />
                </div>
            </form>

        </div>
    </div>
</div>