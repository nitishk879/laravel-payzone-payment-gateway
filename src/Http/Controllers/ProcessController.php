<?php
/** @noinspection PhpUndefinedClassInspection */

namespace Svodya\PayZone\Http\Controllers;

require_once (__DIR__."/../../includes/gateway/paymentsystem.php");

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Payzone\Constants\INTEGRATION_TYPE;
use Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES;
use Payzone\Gateway\PaymentSystem\RequestGatewayEntryPointList;
use Payzone\Gateway\PaymentSystem\ThreeDSecureAuthentication;
use Svodya\PayZone\PayzoneGateway;
use Payzone\Helper\PayzoneHelper;

class ProcessController extends Controller
{
    public $payzoneGateway;
    public $payzoneHelper;
    public function __construct(PayzoneGateway $payzoneGateway, PayzoneHelper $payzoneHelper)
    {
        $SuppressDebug = true;
        $this->payzoneGateway = $payzoneGateway;
        $this->payzoneHelper = $payzoneHelper;
    }

    public function index(Request $request){

        $IntegrationType=$this->payzoneGateway->getIntegrationType();
        $SecretKey=$this->payzoneGateway->getSecretKey();
        $HashMethod=$this->payzoneGateway->getHashMethod();

        if($request->input('PaRes')){
            if($IntegrationType==INTEGRATION_TYPE::DIRECT){
                $this->make3dSecurePayment();
            }
        }

        if($request->input('pzgact')=='process'){
            if($IntegrationType==INTEGRATION_TYPE::DIRECT){
                $this->directPayment($request, $HashMethod, $SecretKey);
            }
        }

    }

    public function directPayment($request , $HashMethod, $SecretKey){
        $paymentResponse = '';
        $CurrencyCode = $this->payzoneGateway->getCurrencyCode();
        $respobj=array();
        $queryObj=array();
        $queryObj["Amount"]=$request->input("Amount");
        $queryObj["CurrencyCode"]=$CurrencyCode;
        $queryObj["OrderID"]=$request->input("OrderID");
        $queryObj["OrderDescription"]=$request->input("OrderDescription");
        $queryObj["HashMethod"]=$HashMethod;
        $StringToHash =  $this->payzoneHelper->generateStringToHashDirect($request->input("Amount"),$CurrencyCode,$request->input("OrderID"),$request->input("OrderDescription"),$SecretKey);
        $ShoppingCartHashDigest = $this->payzoneHelper->calculateHashDigest($StringToHash,$SecretKey,$HashMethod);
        $ShoppingCartValidation = $this->payzoneHelper->checkIntegrityOfIncomingVariablesDirect("SHOPPING_CART_CHECKOUT",$queryObj,$ShoppingCartHashDigest,$SecretKey);
        $respobj["ShoppingCartHashDigest"]=$ShoppingCartHashDigest;
        if ($ShoppingCartValidation){
            $queryObj =  $this->payzoneGateway->buildXHRequest();
            include_once (__DIR__."/../../includes/gateway/direct_process.php");
            echo json_encode($paymentResponse);
        }
        else {
            $paymentResponse["ErrorMessage"]='Hash mismatch validation failure';
            $paymentResponse["ErrorMessages"]=true;
            echo json_encode($paymentResponse);
        }
    }
    public function make3dSecurePayment(){
        // include_once (__DIR__."/../../includes/gateway/threed_process.php");

        $threeD = new RequestGatewayEntryPointList();
        $threeD->add("https://gw1.payzoneonlinepayments.com:4430/", 100, 1);
        $threeD->add("https://gw2.payzoneonlinepayments.com:4430/", 200, 1);
        $threeD->add("https://gw3.payzoneonlinepayments.com:4430/", 300, 1);

        $threeDAuth = new ThreeDSecureAuthentication($threeD);
        $threeDAuth->getMerchantAuthentication()->setMerchantID($this->payzoneGateway->getMerchantId());
        $threeDAuth->getMerchantAuthentication()->setPassword($this->payzoneGateway->getMerchantPassword());

        $threeDAuth->getThreeDSecureInputData()->setCrossReference($_POST['MD']);
        $threeDAuth->getThreeDSecureInputData()->setPaRES($_POST['PaRes']);

        $transProcessed = $threeDAuth->processTransaction($authResult, $transOutput);
        if($transProcessed==false){
            $paymentResponse["Message"] = 'Unable to communicate with the payment gateway.';
            $paymentResponse["StatusCode"] = '99';
        }

        $paymentResponse["Message"] = $authResult->getMessage();
        $paymentResponse["StatusCode"] = $authResult->getStatusCode();

        switch ($authResult->getStatusCode()) {
            case 0:
                // status code of 0 - means transaction successful
                $paymentResponse["CrossReference"] = $transOutput->getCrossReference();
                break;
            case 5:
                // status code of 5 - means transaction declined
                $paymentResponse["CrossReference"] =  $transOutput->getCrossReference();
                break;
            case 20:
                // status code of 20 - means duplicate transaction
                $paymentResponse["CrossReference"] =  $transOutput->getCrossReference();
                $paymentResponse["PreviousTransactionMessage"] = $authResult->getPreviousTransactionResult()->getMessage();
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

        $script = "<script>";
        $script .= "window.parent.postMessage({'option':'iframesrc','value':'three-response'},\"/\");";
        $script .= "window.parent.postMessage({'option':'threedresponse','value':'";
        $script .= json_encode($paymentResponse);
        $script .= "'},\"/\");";
        $script .= "</script>";

        echo $script;
    }

    public function result(Request $request){
        $ThreeDSecure=false;
        $ThreeDSecureResponse=false;
        $payzoneGateway = $this->payzoneGateway;

        if ($request->input("CrossReference") && !$request->input("PaREQ") || $request->input("CrossReference") || $request->input('StatusCode')==30){
            $validate = $this->payzoneGateway->validateResponse($_GET, $_POST);
            if (isset($validate["Notification"])) {
                $showResults=true;
                switch ($validate["Notification"]["Type"]) { //
                    case PAYZONE_RESPONSE_OUTCOMES::SUCCESS: // Payment successful
                        $iframesrc='results';
                        break;
                    case PAYZONE_RESPONSE_OUTCOMES::DUPLICATE:
                    case PAYZONE_RESPONSE_OUTCOMES::ERROR:
                    case PAYZONE_RESPONSE_OUTCOMES::DECLINED:
                        $iframesrc='results';
                        break;
                    case PAYZONE_RESPONSE_OUTCOMES::THREED:
                        $iframesrc='results-threed';
                        //3D Secure Authentication required - don't display results yet but pass over to 3D secure handler
                        $showResults=false;
                        break;
                    case PAYZONE_RESPONSE_OUTCOMES::UNKNOWN:
                    default:
                        $iframesrc='results';
                        # code...
                        break;
                }
                view('payzone::components.response', compact( 'showResults', 'validate', 'payzoneGateway'));
            }
        }
        if ($this->payzoneGateway->getIntegrationType() == INTEGRATION_TYPE::TRANSPARENT ){
            if(isset($_POST["PaREQ"]) && isset($_POST["CrossReference"])){
                $validate3D = $this->payzoneGateway->validateResponse3DTransparent($_POST);
                $showResults=true;
                $validate["Notification"]["Type"]= PAYZONE_RESPONSE_OUTCOMES::THREED;
                $validate["Notification"]["Title"]="3D Secure";
                $validate["Notification"]["Message"]="3D Secure Authentication Required";
                $validate["Response"]["Message"]="";
                if ($validate3D){
                    $iframesrc='results-process';
                    $ThreeDSecure=true;
                }

                view('payzone::components.response.', compact( 'showResults', 'validate', 'payzoneGateway'));

            }
            else if (isset($_POST["PaRes"])){
                $iframesrc='results-process';
                $ThreeDSecureResponse = true;
                $validate3D = $this->payzoneGateway->validateResponse3DTransparentResponse($_POST);
            }
        }

        return view('payzone::success', compact( 'showResults', 'validate', 'payzoneGateway'));
    }
}
