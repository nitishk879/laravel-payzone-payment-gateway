<?php

namespace Svodya\PayZone\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Payzone\Constants\INTEGRATION_TYPE as integrationType;
use Payzone\Helper\PayzoneHelper;
use Svodya\PayZone\PayzoneGateway;

class PayzoneController extends Controller
{
    protected $payZoneGateway;
    protected $payZoneHelper;

    public function __construct(PayzoneGateway $payZoneGateway, PayzoneHelper $payZoneHelper)
    {
        $this->payZoneGateway = $payZoneGateway;
        $this->payZoneHelper = $payZoneHelper;
    }

    public function index()
    {
        $session = array(
            'amount' => 25,
            'order_id' => 12334,
            'order_desc' => "Hey this is my custom order.",
            'customer_name' => 'Geoff Wayne',
            'address_line1' => '113 Glendower Road',
            "address_line2" => '',
            "city" => 'Birmingham',
            "state" => 'West Midlands',
            "postal_code" => 'B42 1SX',
            "country" => array('India', 'United Kingdom', 'America'),
        );

        return view('payzone::index', compact( 'session'));
    }

    public function cart(Request $request)
    {
        $payzoneGateway = $this->payZoneGateway;
        $integrationType = $this->payZoneGateway->getIntegrationType() == integrationType::DIRECT;
        if ($request->has('OrderID')) {
            if(!$request->session()->has('checkout')){
                $this->setter($request, $session=null);
            }
            $country    = $request->Country ?? '826';
        }else{
            $country = 826;
            $session = '';
        }

        return view('payzone::cart', compact('payzoneGateway', 'integrationType', 'country', 'session'));
    }

    public function payment(Request $request)
    {
        $integrationType = $this->payZoneGateway->getIntegrationType() == integrationType::DIRECT;
        if ($request->has('direct_submit')) {
            if (!$request->session()->has('checkout')) {
                redirect()->back()->with("danger", "Sorry! Something went wrong");
            }

            $checkoutSession    = $request->session()->get('checkout')[0] ?? $request->session()->get('checkout');

            $this->setter($request, $checkoutSession);

        } else {
            return redirect()->back()->with('danger', 'Sorry! It seems you\'ve manipulated payment gateway');
        }

        $formBuilder = $this->payZoneGateway->buildFormRequest();

        return view('payzone::payment', compact('integrationType', 'formBuilder'));
    }

    private function setter($request, $session){
        $customer = array(
            "FullAmount" => $session["FullAmount"] ?? $request->input('FullAmount'),
            "Amount" => $session["Amount"] ?? ($session["FullAmount"] ?? $request->input('FullAmount') * 100) / 100,
            "OrderID" => $session["OrderID"] ?? $request->input('OrderID'),
            "TransactionDateTime" => $session["TransactionDateTime"] ?? $request->input("TransactionDateTime"),
            "OrderDescription" => $session["OrderDescription"] ?? $request->input("OrderDescription"),
            "direct_submit" => $request->input('direct_submit'),
            "CustomerName" => $request->input('CustomerName'),
            "Address1" => $request->input('Address1'),
            "Address2" => $request->input('Address2'),
            "City" => $request->input('City'),
            "State" => $request->input('State'),
            "EmailAddress" => $request->input('EmailAddress'),
            "PostCode" => $request->input('PostCode'),
            "Country" => $request->input('Country'),
        );

        return $request->session()->put('checkout', $customer);
    }

}
