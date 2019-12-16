<?php
defined('MAHIDCODE') || die();

if(empty($settings->store_stripe_publishable_key) || empty($settings->store_stripe_secret_key) || empty($settings->store_stripe_webhook_secret)) {
    $_SESSION['info'][] = $language->store->info_message->stripe_not_available;
    User::get_back('store');
}

$method = (isset($parameters[0]) && in_array($parameters[0], ['stripe-success', 'stripe-cancel'])) ? $parameters[0] : false;

/* Return confirmation processing if successfuly */
if($method && $method == 'stripe-success') {
    $_SESSION['success'][] = $language->store->success_message->paid;
    redirect('store');
}

/* Return confirmation processing if failed */
if($method && $method == 'stripe-cancel') {
    $_SESSION['info'][] = $language->store->info_message->canceled;
    redirect('store');
}

/* Initiate Stripe */
\Stripe\Stripe::setApiKey($settings->store_stripe_secret_key);

/* Process form submission */
if(!empty($_POST) && isset($_POST['amount'])) {
    $amount = round(intval($_POST['amount']), 2) * 100;

    /* Create the payment session */
    $stripe_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'name' => $settings->title,
            'description' => $language->store->stripe->description,
            'amount' => $amount,
            'currency' => $settings->store_currency,
            'quantity' => 1,
        ]],
        'client_reference_id' => $account_user_id . '###' . time(),
        'success_url' => url('stripe/stripe-success'),
        'cancel_url' => url('stripe/stripe-cancel'),
    ]);
}