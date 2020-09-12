@if(Session::has('checkout')) @php $checkout = Session::get('checkout'); @endphp
    <div class="card card-details">
        <div class="card-header">
            <div class='payzone-form-section'>
                <h3 class='payzone-form-header'>Card Details</h3>
            </div>
        </div>
        <div class='card-body payzone-form-section'>
            <div class='form-group payzone-form-section'>
                <a href='https://www.payzone.co.uk/' target="_blank">
                    <img class='img-fluid' src="{{ asset("assets/images/payzone_logo.png") }}" alt="Payzone Payment Gateway"/>
                </a>
            </div>
            <div class="form-group">
                <label for='CrossReferenceTransaction'>Use cross reference transaction</label>
                <select id="CrossReferenceTransaction" name="CrossReferenceTransaction" class='custom-select select-70'>
                    <option value='false'>New card</option>
                    <option value='true'>Cross Ref Transaction</option>
                </select>
            </div>
            <div id='CardSectionCRef' style="display:none;">
                <div class="form-group mb-3">
                    <label for='CrossReference'>Cross Reference</label>
                    <input type="text" class="form-control form-control-sm" id="CrossReference" name="CrossReference" value="{{ $checkout->CrossReference ?? '' }}"/>
                </div>
            </div>
            <div id='CardSectionTop'>
                <div class="form-row mb-3">
                    <div class="col-md-6">
                        <label for='CardName'>Card Name</label>
                        <input type="text" class="form-control form-control-sm" id="CardName" name="CardName" value="{{ $checkout['CustomerName'] ?? '' }}"/>
                    </div>
                    <div class="col-md-6">
                        <label for='CardNumber'>Card Number</label>
                        <input type="tel" class="form-control form-control-sm masked" name="CardNumber" id="CardNumber" value="4976 3500 0000 6891" placeholder="XXXX XXXX XXXX XXXX" pattern="\d{4} \d{4} \d{4} \d{4}"/>
                    </div>
                </div>
            </div>
            <div class="form-row mb-3">
                <div class='form-group col-md-12 cvv-wrap'>
                    <label for='CV2'>CV2</label>
                    <input type="text" name="CV2" value="341" id="CV2" maxlength="4" onkeypress='return event.charCode >= 48 && event.charCode <= 57'/>
                </div>
                <div class="form-group col-md-12" id="CardSectionBottom">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for='StartDateMonth'>Start Date</label>
                            <div class="form-row">
                                <select name="StartDateMonth" id="StartDateMonth" class='col-md-4 custom-select select-30'>
                                    <option value=''></option>
                                    @php echo \Payzone\Helper\PayzoneHelper::getMonthList() @endphp
                                </select>
                                <select name="StartDateYear" id="StartDateMonth" class='col-md-8 custom-select select-70'>
                                    <option value=''></option>
                                    @php echo \Payzone\Helper\PayzoneHelper::getStartYearList() @endphp
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label for='ExpiryDateMonth'>Expiry Date</label>
                            <div class="form-row">
                                <select name="ExpiryDateMonth" id="ExpiryDateMonth" class='col-md-4 custom-select select-30'>
                                    <option value=""></option>
                                    @php echo \Payzone\Helper\PayzoneHelper::getMonthList() @endphp
                                </select>
                                <select name="ExpiryDateYear" id="ExpiryDateMonth" class='col-md-8 custom-select select-70'>
                                    <option value=''></option>
                                    @php echo \Payzone\Helper\PayzoneHelper::getExpiryYearList() @endphp
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    </div>
@endif
