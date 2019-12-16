<?php
defined('MAHIDCODE') || die();
User::check_permission(0);

use PayPal\Api\PaymentExecution;
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\InputFields;
use PayPal\Api\WebProfile;
use PayPal\Api\FlowConfig;
use PayPal\Api\Presentation;

if(empty($settings->store_paypal_client_id) || empty($settings->store_paypal_secret)) {
    $_SESSION['info'][] = $language->store->info_message->paypal_not_available;
    User::get_back('store');
}

/* Dealing with generating the payment and redirection to pay */
if(!empty($_POST)) {

    $paypal = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential($settings->store_paypal_client_id, $settings->store_paypal_secret)
    );

    $paypal->setConfig(['mode' => $settings->store_paypal_mode]);

    $product = $settings->title . ' - Points';
    $price = intval($_POST['amount']);
    $shipping = 0;

    $total = $price;

    