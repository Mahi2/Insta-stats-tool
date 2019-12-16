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

    /* Payment experience */
    $flowConfig = new FlowConfig();
    $flowConfig->setLandingPageType('Billing');
    $flowConfig->setUserAction('commit');
    $flowConfig->setReturnUriHttpMethod('GET');

    $presentation = new Presentation();
    $presentation->setBrandName(string_resize($settings->title, 50, $append = ''));

    $inputFields = new InputFields();
    $inputFields->setAllowNote(true)
        ->setNoShipping(1)
        ->setAddressOverride(0);

    $webProfile = new WebProfile();
    $webProfile->setName(string_resize($settings->title, 25, $append = '') . uniqid())
        ->setFlowConfig($flowConfig)
        ->setPresentation($presentation)
        ->setInputFields($inputFields)
        ->setTemporary(true);

        /* Create the experience profile */
    try {
        $createdProfileResponse = $webProfile->create($paypal);
    } catch (\PayPal\Exception\PayPalConnectionException $ex) {
        echo $ex->getCode();
        echo $ex->getData();

        die($ex);
    }

    $payer = new Payer();
    $payer->setPaymentMethod('paypal');

    $item = new Item();
    $item->setName($product)
        ->setCurrency($settings->store_currency)
        ->setQuantity(1)
        ->setPrice($price);

    $itemList = new ItemList();
    $itemList->setItems([$item]);


    $amount = new Amount();
    $amount->setCurrency($settings->store_currency)
        ->setTotal($total);

    $transaction = new Transaction();
    $transaction->setAmount($amount)
        ->setItemList($itemList)
        ->setInvoiceNumber(uniqid());

    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($settings->url . 'paypal?success=true')
        ->setCancelUrl($settings->url . 'paypal?success=false');

    $payment = new Payment();
    $payment->setIntent('sale')
        ->setPayer($payer)
        ->setRedirectUrls($redirectUrls)
        ->setTransactions([$transaction])
        ->setExperienceProfileId($createdProfileResponse->getId());

    try {
        $payment->create($paypal);
    } catch (Exception $ex) {
        echo $ex->getCode();
        echo $ex->getData();

        die($ex);
    }

    $approvalUrl = $payment->getApprovalLink();

    header('Location: ' . $approvalUrl);
}

/* Dealing with the details after the payment has been processed */
if(isset($_GET['success'], $_GET['paymentId'], $_GET['PayerID']) && $_GET['success'] == 'true') {

    $paypal = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential($settings->store_paypal_client_id, $settings->store_paypal_secret)
    );

    $paypal->setConfig(['mode' => $settings->store_paypal_mode]);

    $payment_id = $_GET['paymentId'];
    $payer_id = $_GET['PayerID'];

    /* First make sure the payment is not already existing */
    if(Database::exists(['id'], 'payments', ['payment_id' => $payment_id, 'payer_id' => $payer_id])) {
        redirect('store');
    }

    try {
        $payment = Payment::get($payment_id, $paypal);

        $payer_info = $payment->getPayer()->getPayerInfo();
        $payer_email = $payer_info->getEmail();
        $payer_name = $payer_info->getFirstName() . ' ' . $payer_info->getLastName();

        $transactions = $payment->getTransactions();
        $amount = $transactions[0]->getAmount();
        $amount_total = $amount->getTotal();
        $amount_currency = $amount->getCurrency();

        $execute = new PaymentExecution();
        $execute->setPayerId($payer_id);

        $result = $payment->execute($execute, $paypal);

    } catch (Exception $ex) {
        $data = json_decode($ex->getData());
    }

    /* If the $data variable is not set, there is no error in the payment processing */
    if(!isset($data)) {
        /* Add a log into the database */
        Database::insert(
            'payments',
            [
                'user_id' => $account_user_id,
                'type' => 'PAYPAL',
                'email' => $payer_email,
                'payment_id' => $payment_id,
                'payer_id' => $payer_id,
                'name' => $payer_name,
                'amount' => $amount_total,
                'currency' => $amount_currency,
                'date' => $date
            ]
        );