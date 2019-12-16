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