<?php
/* Init stripe */
\Stripe\Stripe::setApiKey($settings->store_stripe_secret_key);


?>

<div class="d-flex justify-content-center">
    <div class="card card-shadow animated fadeIn col-xs-12 col-sm-10 col-md-6 col-lg-4">
        <div class="card-body">


            <h4 class="d-flex justify-content-between">
                <?= $language->store->stripe->header ?>
                <small><?= User::generate_go_back_button('store') ?></small>
            </h4>


            <form action="stripe" method="post" role="form">
                <div class="form-group mt-5">

                    <div class="form-group mt-5">
                        <label><?= $language->store->stripe->amount ?></label>
                        <input class="form-control" type="number" name="amount" value="5" min="1" />
                        <small class="text-muted"><?= sprintf($language->store->display->info, $settings->store_currency) ?></small>
                    </div>

                </div>

                <button id="pay" class="mt-5 btn btn-default btn-block"><?= $language->store->button->pay ?></button>
            </form>


        </div>
    </div>
</div>

<?php

/* Include only if the stripe redirect session was generated */
if(isset($stripe_session)):

    ?>
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        let stripe = Stripe(<?= json_encode($settings->store_stripe_publishable_key) ?>);

        stripe.redirectToCheckout({
            sessionId: <?= json_encode($stripe_session->id) ?>,
        }).then((result) => {

            /* Nothing for the moment */

        });
    </script>

<?php endif ?>