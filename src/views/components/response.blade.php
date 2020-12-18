<div class="card p-2">
    @if($showResults)
        <div class="payzone-about">
            @if($validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::SUCCESS || $validate["Notification"]["Type"]== \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::DECLINED || $validate["Notification"]["Type"]== \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::DUPLICATE || $validate["Notification"]["Type"]== \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::THREED || $validate["Notification"]["Type"]== \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::ERROR || $validate["Notification"]["Type"]== \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::UNKNOWN)
                <div class='card-body payzone-transaction-results'>
                    <div class='payzone-results-section pz-left'>
                        <a href='https://www.payzone.co.uk/' target="_blank">
                            <img class='img-fluid payzone-logo' src="{{ asset("/assets/images/payzone_logo.png") }}" alt="payzone"/>
                        </a>
                    </div>
                    <div class='pt-3 payzone-results-section {{ $validate["Notification"]["Class"] }}'>
                        <h4 class='text-center payzone-results-header'>Transaction Status</h4>
                        @if($validate["Notification"]["Type"] == \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::DUPLICATE)
                            <p class='payzone-results-sub-header'>{{ $validate["Notification"]["Title"] }}</p>
                            <p>{{ $validate["Notification"]["Message"] }}</p>
                            <p class='payzone-results-details'>Previous Response: {{ $validate["Response"]["PreviousResponse"]["PreviousMessage"] }}</p>
                        @else
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>{{ $validate["Notification"]["Title"] }}!</strong> {{ $validate["Notification"]["Message"] }}
                                <br/>
                                {{ $validate["Response"]["Message"] }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @if($validate["Notification"]["Type"] == \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::ERROR || $validate["Notification"]["Type"] == \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::UNKNOWN)
                                <p class='payzone-results-details'>{{ $validate["Response"]["ErrorMessages"] }}</p>
                            @endif
                        @endif
                    </div>
                    @if($payzoneGateway->getOrderDetails() && !($validate["Notification"]["Type"] == \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::THREED))
                        <hr>
                        <div class='list-group payzone-results-section'>
                            <li class='list-group-item list-group-item-dark payzone-results-sub-header'>Order Details</li>
                            <li class='list-group-item list-group-item-dark payzone-results-details'>Order ID: {{ $validate["Order"]["OrderID"] }}</li>
                            <li class='list-group-item list-group-item-dark payzone-results-details'>Amount: {!! $validate["Order"]["Amount"] !!}
                                @php echo ($validate["Order"]["TransactionType"] == \Payzone\Constants\TRANSACTION_TYPE::PREAUTH && $validate["Notification"]["Type"] == \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::SUCCESS) ? " (Pre-authorised)" : "" @endphp
                            </li>
                            @isset($validate["Notification"]["Refund"])
                                @if($validate["Notification"]["Refund"] != "Refund")
                                    <li class='list-group list-group-item-dark payzone-results-details'>Description: {{ $validate["Order"]["OrderDescription"] }}</li>
                                @endif
                            @endisset
                        </div>
                    @endif
                    <hr>
                    <div class="card-footer">
                        <a class="btn btn-outline-secondary btn-block" target="_top" href='/'>Return to home page</a>
                    </div>
                    <div class='payzone-results-section'>
                        <a href='https://www.payzone.co.uk/' target="_blank">
                            <img class='payzone-logo-footer img-fluid' src="{{ asset("/assets/images/payzone_secure_badge.png") }}" alt="Secure Card"/>
                        </a>
                    </div>
                </div>
            @endif
            @if($validate["Notification"]["Type"] == \Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::ERROR)
                <div class='card-body payzone-transaction-results'>
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
