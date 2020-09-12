<?php /**
 * Payzone Payment Gateway
 * ========================================
 * Web:   http://payzone.co.uk
 * Email:  online@payzone.com
 * Authors: Payzone, Keith Rigby
 */

if (count(get_included_files()) == 1) {
    exit("Direct access not permitted.");
}

require_once(__DIR__ . "/../gateway/paymentsystem.php");

use \Payzone\Gateway\PaymentSystem as PaymentSystem;


$rgeplRequestGatewayEntryPointList = new PaymentSystem\RequestGatewayEntryPointList();
$rgeplRequestGatewayEntryPointList->add("https://gw1.payzoneonlinepayments.com:4430/", 100, 1);
$rgeplRequestGatewayEntryPointList->add("https://gw2.payzoneonlinepayments.com:4430/", 200, 1);
$rgeplRequestGatewayEntryPointList->add("https://gw3.payzoneonlinepayments.com:4430/", 300, 1);

if ($queryObj["CrossReferenceTransaction"] != false) {
    $cdtCardDetailsTransaction = new  PaymentSystem\CrossReferenceTransaction($rgeplRequestGatewayEntryPointList);
} else {
    $cdtCardDetailsTransaction = new  PaymentSystem\CardDetailsTransaction($rgeplRequestGatewayEntryPointList);
}

$cdtCardDetailsTransaction->getMerchantAuthentication()->setMerchantID($this->payzoneGateway->getMerchantID());
$cdtCardDetailsTransaction->getMerchantAuthentication()->setPassword($this->payzoneGateway->getMerchantPassword());
$cdtCardDetailsTransaction->getTransactionDetails()->getMessageDetails()->setTransactionType($this->payzoneGateway->getTransactionType());
$cdtCardDetailsTransaction->getTransactionDetails()->getAmount()->setValue($queryObj["Amount"]);
$cdtCardDetailsTransaction->getTransactionDetails()->getCurrencyCode()->setValue($queryObj["CurrencyCode"]);
$cdtCardDetailsTransaction->getTransactionDetails()->setOrderID($queryObj["OrderID"]);
$cdtCardDetailsTransaction->getTransactionDetails()->setOrderDescription($queryObj["OrderDescription"]);
$cdtCardDetailsTransaction->getTransactionDetails()->getTransactionControl()->getEchoCardType()->setValue($queryObj["EchoCardType"]);
$cdtCardDetailsTransaction->getTransactionDetails()->getTransactionControl()->getEchoAmountReceived()->setValue(true);
$cdtCardDetailsTransaction->getTransactionDetails()->getTransactionControl()->getEchoAVSCheckResult()->setValue($queryObj["EchoAVSCheckResult"]);
$cdtCardDetailsTransaction->getTransactionDetails()->getTransactionControl()->getEchoCV2CheckResult()->setValue($queryObj["EchoCV2CheckResult"]);
$cdtCardDetailsTransaction->getTransactionDetails()->getTransactionControl()->getThreeDSecureOverridePolicy()->setValue(true);
$cdtCardDetailsTransaction->getTransactionDetails()->getTransactionControl()->getDuplicateDelay()->setValue(60);
$cdtCardDetailsTransaction->getTransactionDetails()->getThreeDSecureBrowserDetails()->getDeviceCategory()->setValue(0);
$cdtCardDetailsTransaction->getTransactionDetails()->getThreeDSecureBrowserDetails()->setAcceptHeaders("*/*");
$cdtCardDetailsTransaction->getTransactionDetails()->getThreeDSecureBrowserDetails()->setUserAgent($_SERVER["HTTP_USER_AGENT"]);

if ($queryObj["CrossReferenceTransaction"] != false) {
    $cdtCardDetailsTransaction->getTransactionDetails()->getMessageDetails()->setCrossReference($queryObj["CrossReferenceTransactionID"]);
    $cdtCardDetailsTransaction->getOverrideCardDetails()->setCV2($queryObj["CV2"]);
    $cdtCardDetailsTransaction->getTransactionDetails()->getTransactionControl()->getThreeDSecureOverridePolicy()->setValue(true);
} else {
    $cdtCardDetailsTransaction->getCardDetails()->setCardName($queryObj["CustomerName"]);
    $cdtCardDetailsTransaction->getCardDetails()->setCardNumber($queryObj["CardNumber"]);
    $cdtCardDetailsTransaction->getCardDetails()->getExpiryDate()->getMonth()->setValue($queryObj["ExpiryDateMonth"]);
    $cdtCardDetailsTransaction->getCardDetails()->getExpiryDate()->getYear()->setValue($queryObj["ExpiryDateYear"]);
    $cdtCardDetailsTransaction->getCardDetails()->getStartDate()->getMonth()->setValue($queryObj["ExpiryDateMonth"]);
    $cdtCardDetailsTransaction->getCardDetails()->getStartDate()->getYear()->setValue($queryObj["ExpiryDateMonth"]);
    $cdtCardDetailsTransaction->getCardDetails()->setIssueNumber($queryObj["IssueNumber"]);
    $cdtCardDetailsTransaction->getCardDetails()->setCV2($queryObj["CV2"]);
}

$cdtCardDetailsTransaction->getCustomerDetails()->getBillingAddress()->setAddress1($queryObj["Address1"]);
$cdtCardDetailsTransaction->getCustomerDetails()->getBillingAddress()->setAddress2($queryObj["Address2"]);
$cdtCardDetailsTransaction->getCustomerDetails()->getBillingAddress()->setAddress3($queryObj["Address3"]);
$cdtCardDetailsTransaction->getCustomerDetails()->getBillingAddress()->setAddress4($queryObj["Address4"]);
$cdtCardDetailsTransaction->getCustomerDetails()->getBillingAddress()->setCity($queryObj["City"]);
$cdtCardDetailsTransaction->getCustomerDetails()->getBillingAddress()->setState($queryObj["State"]);
$cdtCardDetailsTransaction->getCustomerDetails()->getBillingAddress()->setPostCode($queryObj["PostCode"]);

$cdtCardDetailsTransaction->getCustomerDetails()->getBillingAddress()->getCountryCode()->setValue($queryObj["CountryCode"]);

$cdtCardDetailsTransaction->getCustomerDetails()->setEmailAddress($queryObj["EmailAddress"]);
$cdtCardDetailsTransaction->getCustomerDetails()->setPhoneNumber("");
$cdtCardDetailsTransaction->getCustomerDetails()->setCustomerIPAddress("");
$cdtCardDetailsTransaction->getTransactionDetails()->getTransactionControl()->getThreeDSecureOverridePolicy()->setValue(true);

$boTransactionProcessed = $cdtCardDetailsTransaction->processTransaction($cdtrCardDetailsTransactionResult, $todTransactionOutputData);
$paymentResponse = array();

if ($boTransactionProcessed == false) {
    // could not communicate with the payment gateway
    $paymentResponse["Message"] = 'Unable to communicate with the payment gateway.';
    $paymentResponse["StatusCode"] = '99';
} else {
    $paymentResponse["Message"] = $cdtrCardDetailsTransactionResult->getMessage();
    $paymentResponse["StatusCode"] = $cdtrCardDetailsTransactionResult->getStatusCode();

    switch ($cdtrCardDetailsTransactionResult->getStatusCode()) {
        case 0:
            // status code of 0 - means transaction successful
            $paymentResponse["CrossReference"] = $todTransactionOutputData->getCrossReference();
            break;
        case 3:
            // status code of 3 - means 3D Secure authentication required
            $paymentResponse["ThreeDSecure"] = true;
            $paymentResponse["CrossReference"] = $todTransactionOutputData->getCrossReference();
            $paymentResponse["PaREQ"] = $todTransactionOutputData->getThreeDSecureOutputData()->getPaREQ();
            $paymentResponse["ACSURL"] = $todTransactionOutputData->getThreeDSecureOutputData()->getACSURL();
            $paymentResponse["TermUrl"] = $this->payzoneGateway->getURL('process-payment');
            break;
        case 4:
        case 5:
            // status code of 5 - means transaction declined
            $paymentResponse["CrossReference"] = $todTransactionOutputData->getCrossReference();
            break;
        case 20:
            // status code of 20 - means duplicate transaction
            $paymentResponse["CrossReference"] = $todTransactionOutputData->getCrossReference();
            $paymentResponse["PreviousTransactionMessage"] = $cdtrCardDetailsTransactionResult->getPreviousTransactionResult()->getMessage();
            $paymentResponse["DuplicateTransaction"] = true;
            break;
        case 30:
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
