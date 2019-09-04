<?php

use Shipu\Bkash\Enums\BkashKey;
use Shipu\Bkash\Enums\BkashSubDomainType;

require_once './../composer_load.php';

$tokenized = new Shipu\Bkash\Managers\Tokenized($config[BkashSubDomainType::TOKENIZED]);

$data = $_GET;

if($data['execute'] == 'Agreement') {
    $agreement = $tokenized->executeAgreement($data['paymentID']);

    $newQueryString = [
        'amount' => $data['amount'],
        'order_code' => $data['order_code'],
        'payerReference' => $data['payerReference'],
        'success_url' => $data['success_url'],
        'failed_url' => $data['failed_url']
    ];

    $callbackUrl = $config[BkashSubDomainType::TOKENIZED][BkashKey::CALL_BACK_URL].'?'.http_build_query($data);

    // Save Agreement Id ($agreement->agreementID)
    $createPayment = $tokenized->createPayment(
        $agreement->agreementID,
        $data['amount'],
        $data['ref_no'],
        $callbackUrl
    );

    header('Location: '.$createPayment->bkashURL);
} elseif ($data['execute'] == 'Payment') {
    $payment = $tokenized->executePayment($data['paymentID']);

    if($payment->statusCode == '0000') {
        // Save "trxID" & create payment
        dd($payment->trxID);
        header('Location: '.$data['success_url']);
    } else {
        header('Location: '.$data['failed_url']);
    }
}




