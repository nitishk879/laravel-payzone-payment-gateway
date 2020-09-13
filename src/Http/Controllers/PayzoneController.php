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
            $session = array(
                "FullAmount" => $request->input("FullAmount"),
                "Amount" => (($request->input("FullAmount") * 100) / 100),
                "OrderID" => $request->input("OrderID"),
                "TransactionDateTime" => $request->input("TransactionDateTime"),
                "OrderDescription" => $request->input("OrderDescription"),
                "CustomerName" => $request->input("CustomerName") ?? '',
                "Address1" => $request->input("Address1") ?? '',
                "Address2" => $request->input("Address2") ?? '',
                "City" => $request->input("City") ?? '',
                "State" => $request->input("State") ?? '',
                "PostCode" => $request->input("PostCode") ?? '',
                "Country"   => $request->input('Country') ?? ''
            );
            if (!$request->session()->has('checkout')) {
                $request->session()->put('checkout', $session);
            }

            $country    = $request->Country ?? '826';
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

            $orderStatus = $request->session()->get('checkout')[0] ?? $request->session()->get('checkout');

            $customer = array(
                "FullAmount" => $orderStatus["FullAmount"],
                "Amount" => $orderStatus["Amount"] ?? ($orderStatus["FullAmount"] * 100) / 100,
                "OrderID" => $orderStatus["OrderID"],
                "TransactionDateTime" => $orderStatus["TransactionDateTime"],
                "OrderDescription" => $orderStatus["OrderDescription"],
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

            $request->session()->put('checkout', $customer);
        } else {
            return redirect()->back()->with('danger', 'Sorry! It seems you\'ve manipulated payment gateway');
        }

        $formBuilder = $this->payZoneGateway->buildFormRequest();

        return view('payzone::payment', compact('integrationType', 'formBuilder'));
    }

}
