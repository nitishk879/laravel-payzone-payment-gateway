@extends('payzone::template')

@section('title', 'Success!')

@section('onload', "payzoneResultsOnload();")

@section('content')
    <div class="">
        @if($showresults)
            <div class="payzone-about">
                @if($validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::SUCCESS || $validate["Notification"]["Type"]== \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::DECLINED || $validate["Notification"]["Type"]== \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::DUPLICATE || $validate["Notification"]["Type"]== \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::THREED || $validate["Notification"]["Type"]== \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::ERROR || $validate["Notification"]["Type"]== \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::UNKNOWN)
                    <div class='payzone-transaction-results'>
                        <div class='payzone-results-section pz-left'>
                            <a href='https://www.payzone.co.uk/' target="_blank">
                                <img class='img-fluid payzone-logo' src="{{ asset("assets/images/payzone_logo.png") }}" alt="payzone"/>
                            </a>
                        </div>
                        <div class='payzone-results-section {{ $validate["Notification"]["Class"] }}'>
                            <p class='payzone-results-header'>Transaction Status</p>
                            @if($validate["Notification"]["Type"] == \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::DUPLICATE)
                                <p class='payzone-results-sub-header'>{{ $validate["Notification"]["Title"] }}</p>
                                <p>{{ $validate["Notification"]["Message"] }}</p>
                                <p class='payzone-results-details'>Previous Response: {{ $validate["Response"]["PreviousResponse"]["PreviousMessage"] }}</p>
                            @else
                                <p class='payzone-results-sub-header'>{{ $validate["Notification"]["Title"] }} </p>
                                <p>{{ $validate["Notification"]["Message"] }} </p>
                                <p class='payzone-results-details'>{{ $validate["Response"]["Message"] }}</p>
                                @if($validate["Notification"]["Type"] == \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::ERROR || $validate["Notification"]["Type"] == \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::UNKNOWN)
                                    <p class='payzone-results-details'>{{ $validate["Response"]["ErrorMessages"] }}</p>
                                @endif
                            @endif
                        </div>
                        @if($payzoneGateway->getOrderDetails() && !($validate["Notification"]["Type"] == \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::THREED))
                            <hr>
                            <div class='payzone-results-section'>
                                <p class='payzone-results-sub-header'>Order Details</p>
                                <p class='payzone-results-details'>Order ID: {{ $validate["Order"]["OrderID"] }}</p>
                                <p class='payzone-results-details'>Amount: {{ $validate["Order"]["Amount"] }}
                                    @php echo ($validate["Order"]["TransactionType"] == \Payzone\Constants\TRANSACTION_TYPE::PREAUTH && $validate["Notification"]["Type"] == \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::SUCCESS) ? " (Pre-authorised)" : "" @endphp
                                </p>
                                @if($validate["Notification"]["Refund"] != "Refund")
                                    <p class='payzone-results-details'>Description: {{ $validate["Order"]["OrderDescription"] }}</p>
                                @endif
                            </div>
                        @endif
                        <hr>
                        <a target="_top" href='/'>Return to home page</a>
                        <div class='payzone-results-section'>
                            <a href='https://www.payzone.co.uk/' target="_blank">
                                <img class='payzone-logo-footer' src="{{ asset("assets/images/payzone_secure_badge.png") }}" alt="Secure Card"/>
                            </a>
                        </div>
                    </div>
                @endif
                    @if($validate["Notification"]["Type"] == \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::ERROR)
                        <div class='payzone-transaction-results'>
                            <div class='payzone-results-section pz-left'>
                                <a href='https://www.payzone.co.uk/' target="_blank">
                                    <img class='img-fluid payzone-logo' src="{{ asset("assets/images/payzone_logo.png") }}" alt="payzone"/>
                                </a>
                            </div>
                            <div class='payzone-results-section'>
                                <p class='payzone-results-header'>Unable to process transaction</p>
                                <p class='payzone-results-details'>Gateway Response:</p>
                                <p class='payzone-results-details'>{{ $validate["Notification"]["Message"] }}</p>
                            </div>
                            <hr>
                            <a target="_top" href='/'>Return to home page</a>
                            <div class='payzone-results-section'>
                                <a href='https://www.payzone.co.uk/' target="_blank">
                                    <img class='payzone-logo-footer' src="{{ asset("assets/images/payzone_secure_badge.png") }}" alt="Secure Card"/>
                                </a>
                            </div>
                        </div>
                    @endif
            </div>
        @endif
    </div>
@endsection

@section('scripts')@endsection
