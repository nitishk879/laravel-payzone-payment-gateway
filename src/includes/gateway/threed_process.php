<?php

require_once(__DIR__ . "/../gateway/paymentsystem.php");

use \Payzone\Gateway\PaymentSystem as PaymentSystem;

$rgeplRequestGatewayEntryPointList = new  PaymentSystem\RequestGatewayEntryPointList();
$rgeplRequestGatewayEntryPointList->add("https://gw1.payzoneonlinepayments.com:4430/", 100, 1);
$rgeplRequestGatewayEntryPointList->add("https://gw2.payzoneonlinepayments.com:4430/", 200, 1);
$rgeplRequestGatewayEntryPointList->add("https://gw3.payzoneonlinepayments.com:4430/", 300, 1);

$tdsaThreeDSecureAuthentication = new  PaymentSystem\ThreeDSecureAuthentication($rgeplRequestGatewayEntryPointList);

$tdsaThreeDSecureAuthentication->getMerchantAuthentication()->setMerchantID($this->payzoneGateway->getMerchantID());
$tdsaThreeDSecureAuthentication->getMerchantAuthentication()->setPassword($this->payzoneGateway->getMerchantPassword());

$tdsaThreeDSecureAuthentication->getThreeDSecureInputData()->setCrossReference($_POST["MD"]);
$tdsaThreeDSecureAuthentication->getThreeDSecureInputData()->setPaRES($_POST["PaRes"]);

$boTransactionProcessed = $tdsaThreeDSecureAuthentication->processTransaction($tdsarThreeDSecureAuthenticationResult, $todTransactionOutputData);

if ($boTransactionProcessed == false) {
    // could not communicate with the payment gateway
    $paymentResponse["Message"] = 'Unable to communicate with the payment gateway.';
    $paymentResponse["StatusCode"] = '99';
} else {
    $paymentResponse["Message"]     = $tdsarThreeDSecureAuthenticationResult->getMessage();
    $paymentResponse["StatusCode"]  = $tdsarThreeDSecureAuthenticationResult->getStatusCode();

    switch ($tdsarThreeDSecureAuthenticationResult->getStatusCode()) {
        case 0:
            // status code of 0 - means transaction successful
            $paymentResponse["CrossReference"] = $todTransactionOutputData->getCrossReference();
            break;
        case 5:
            // status code of 5 - means transaction declined
            $paymentResponse["CrossReference"] = $todTransactionOutputData->getCrossReference();
            break;
        case 20:
            // status code of 20 - means duplicate transaction
            $paymentResponse["CrossReference"] = $todTransactionOutputData->getCrossReference();
            $paymentResponse["PreviousTransactionMessage"] = $tdsarThreeDSecureAuthenticationResult->getPreviousTransactionResult()->getMessage();
            $paymentResponse["DuplicateTransaction"] = true;
            break;
        case 30:
            // status code of 30 - means an error occurred
            $paymentResponse["ErrorMessages"] = "";
            // status code of 30 - means an error occurred
            $eCount = $cdtrCardDetailsTransactionResult->getErrorMessages()->getCount();
            if ($eCount > 0) {
                for ($i = 0; $i < $eCount; $i++) {
                    $paymentResponse["ErrorMessages"] .= $cdtrCardDetailsTransactionResult->getErrorMessages()->getAt($i);
                }
            }
            break;
        default:
            // unhandled status code

            break;
    }
}
