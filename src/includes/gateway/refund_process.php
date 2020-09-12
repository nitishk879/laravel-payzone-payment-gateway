<?php /**
* Payzone Payment Gateway
* ========================================
* Web:   http://payzone.co.uk
* Email:  online@payzone.com
* Authors: Payzone, Keith Rigby
*/

if (count(get_included_files()) ==1) {
    exit("Direct access not permitted.");
}

require_once (__DIR__."/../gateway/paymentsystem.php");

use \Payzone\Gateway\PaymentSystem as PaymentSystem;


$rgeplRequestGatewayEntryPointList = new PaymentSystem\RequestGatewayEntryPointList();
// you need to put the correct gateway entry point urls in here
// contact support to get the correct urls

// The actual values to use for the entry points can be established in a number of ways
// 1) By periodically issuing a call to GetGatewayEntryPoints
// 2) By storing the values for the entry points returned with each transaction
// 3) Speculatively firing transactions at https://gw1.xxx followed by gw2, gw3, gw4....
// The lower the metric (2nd parameter) means that entry point will be attempted first,
// EXCEPT if it is -1 - in this case that entry point will be skipped
// NOTE: You do NOT have to add the entry points in any particular order - the list is sorted
// by metric value before the transaction sumbitting process begins
// The 3rd parameter is a retry attempt, so it is possible to try that entry point that number of times
// before failing over onto the next entry point in the list
$rgeplRequestGatewayEntryPointList->add("https://gw1.payzoneonlinepayments.com:4430/", 100, 1);
$rgeplRequestGatewayEntryPointList->add("https://gw2.payzoneonlinepayments.com:4430/", 200, 1);
$rgeplRequestGatewayEntryPointList->add("https://gw3.payzoneonlinepayments.com:4430/", 300, 1);


$cdtCardDetailsTransaction = new  PaymentSystem\CrossReferenceTransaction($rgeplRequestGatewayEntryPointList);

$cdtCardDetailsTransaction->getMerchantAuthentication()->setMerchantID($PayzoneGateway->getMerchantID());
$cdtCardDetailsTransaction->getMerchantAuthentication()->setPassword($PayzoneGateway->getMerchantPassword());

$cdtCardDetailsTransaction->getTransactionDetails()->getMessageDetails()->setTransactionType('REFUND');

$cdtCardDetailsTransaction->getTransactionDetails()->getAmount()->setValue($queryObj["Amount"]);
$cdtCardDetailsTransaction->getTransactionDetails()->getCurrencyCode()->setValue($queryObj["CurrencyCode"]);


$cdtCardDetailsTransaction->getTransactionDetails()->setOrderID($queryObj["OrderID"]);

$cdtCardDetailsTransaction->getTransactionDetails()->getTransactionControl()->getDuplicateDelay()->setValue(60);

$cdtCardDetailsTransaction->getTransactionDetails()->getThreeDSecureBrowserDetails()->getDeviceCategory()->setValue(0);
$cdtCardDetailsTransaction->getTransactionDetails()->getThreeDSecureBrowserDetails()->setAcceptHeaders("*/*");
$cdtCardDetailsTransaction->getTransactionDetails()->getThreeDSecureBrowserDetails()->setUserAgent($_SERVER["HTTP_USER_AGENT"]);

$cdtCardDetailsTransaction->getTransactionDetails()->getMessageDetails()->setCrossReference($queryObj["CrossReference"]);

$cdtCardDetailsTransaction->getTransactionDetails()->getTransactionControl()->getThreeDSecureOverridePolicy()->setValue(true);

$boTransactionProcessed = $cdtCardDetailsTransaction->processTransaction($cdtrCardDetailsTransactionResult, $todTransactionOutputData);
$paymentResponse=array();

if ($boTransactionProcessed == false)
{
	// could not communicate with the payment gateway
	$paymentResponse["Message"]='Unable to communicate with the payment gateway.';
	$paymentResponse["StatusCode"]='99';
}
else
{
	$paymentResponse["Message"]=$cdtrCardDetailsTransactionResult->getMessage();
	$paymentResponse["StatusCode"]=$cdtrCardDetailsTransactionResult->getStatusCode();

	switch ($cdtrCardDetailsTransactionResult->getStatusCode())
	{
		case 0:
		// status code of 0 - means transaction successful
		$paymentResponse["CrossReference"]=$todTransactionOutputData->getCrossReference();
		break;
		case 3:
		// status code of 3 - means 3D Secure authentication required
		$paymentResponse["ThreeDSecure"]=true;
		$paymentResponse["CrossReference"]=$todTransactionOutputData->getCrossReference();
		$paymentResponse["PaREQ"]= $todTransactionOutputData->getThreeDSecureOutputData()->getPaREQ();
		$paymentResponse["ACSURL"]=$todTransactionOutputData->getThreeDSecureOutputData()->getACSURL();
		$paymentResponse["TermUrl"]=$PayzoneGateway->getURL('process-payment');
		break;
		case 4:
		case 5:
		// status code of 5 - means transaction declined
		$paymentResponse["CrossReference"]=$todTransactionOutputData->getCrossReference();
		break;
		case 20:
		// status code of 20 - means duplicate transaction
		$paymentResponse["CrossReference"]=$todTransactionOutputData->getCrossReference();
		$paymentResponse["PreviousTransactionMessage"]=$cdtrCardDetailsTransactionResult->getPreviousTransactionResult()->getMessage();
		$paymentResponse["DuplicateTransaction"]= true;
		break;
		case 30:
		$paymentResponse["ErrorMessages"] = "";
		// status code of 30 - means an error occurred
		$eCount = $cdtrCardDetailsTransactionResult->getErrorMessages()->getCount();
		if ($eCount > 0) {
			for ($i = 0; $i < $eCount; $i++) {
				$paymentResponse["ErrorMessages"].=$cdtrCardDetailsTransactionResult->getErrorMessages()->getAt($i)."<br>";




			}
		}
		break;
		default:
		// unhandled status code

		break;
	}
}
?>
