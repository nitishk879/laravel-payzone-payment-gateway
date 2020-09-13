@extends('payzone::template')

@section('title', 'lET\'S PAY WITH FILLING Card-details')

@section('onload', "payzonePaymentPageLoad();")

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <form class='payzone-form' id='payzone-payment-form' name='payzone-payment-form' target="_self" method="POST"  action="/callback-url" >
                    @csrf
                    @if($integrationType)
                        @include("payzone::components.card-details")
                        <span id='form_errors'></span>
                        <div class='payzone-form-section'>
                            <input id='payzone-direct' type="hidden" name="payzone-direct" value="submitted" />
                            <input id='payzone-cart-submit' type="submit" name="Submit" value="Submit" />
                        </div>
                        <div class="form-group">
                            <div class='payzone-form-section'>
                                <a href="https://www.payzone.co.uk/" target="_blank">
                                    <img class='img-fluid payzone-footer-image' src="{{ asset("/assets/images/payzone_cards_accepted.png") }}" alt="card Accepted" />
                                </a>
                            </div>
                        </div>
                    @endif
                    @php echo $formBuilder @endphp
                </form>
            </div>
        </div>
    </div>
@endsection

@section('model')
    @include('payzone::components.payzone-modal')
@endsection

@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset("/assets/inputmasking.js") }}" data-autoinit="true"></script>
    <script src="{{ asset("/assets/payzone_validate.js") }}" data-autoinit="true"></script>
    <script>
        PayzonePaymentForm = document.getElementById('payzone-payment-form');
        function payzonePaymentPageLoad(){
            @if($integrationType)
                PayzonePaymentForm.style.display='block';
                window.parent.postMessage({'option':'modalsize','value':'5'},"/");
                window.parent.postMessage({'option':'iframesrc','value':'payment'},"/");
            @endif
        }
    </script>
    @if($integrationType)
        <script>
            var iframepage='payment';
            document.getElementById("payzone-payment-modal-background").addEventListener("click", closePayzoneModal);
            document.getElementById("payzone-modal-close").addEventListener("click", closePayzoneModal);
            document.getElementById("payzone-cart-submit").addEventListener("click", payzoneCartFormSubmission );
            document.getElementById("CrossReferenceTransaction").addEventListener("change", CrossReferenceTransaction);

            function CrossReferenceTransaction(evt){
                var CrossRefTrans = (document.getElementById("CrossReferenceTransaction").value === 'false') ? 'false': 'true';
                if (CrossRefTrans==='true'){
                    document.getElementById('CardSectionTop').style.display='none';
                    document.getElementById('CardSectionBottom').style.display='none';
                    document.getElementById('CardSectionCRef').style.display='block';
                }else {
                    document.getElementById('CardSectionTop').style.display='block';
                    document.getElementById('CardSectionBottom').style.display='block';
                    document.getElementById('CardSectionCRef').style.display='none';
                }
            }

            function payzoneCartFormSubmission(evt){
                evt.preventDefault();
                window.addEventListener("message", receiveMessageCart, false);
                var cartForm = document.getElementById('payzone-payment-form');
                var pzgModal    = document.getElementById('payzone-payment-modal');
                openLoadingModal('loading');
                var validated = validateForm(cartForm,'direct','true');

                if (validated){
                    var cartData = new FormData(cartForm);
                    var XHR = new XMLHttpRequest();
                    XHR.open("POST", "/payment-process?pzgact=process", true);
                    XHR.onreadystatechange = function() {
                        if (this.readyState === 4 && this.status === 200) {
                            responseObj = JSON.parse((this.responseText))
                            if (responseObj["StatusCode"]===3){
                                openPayzoneModal(1);
                                var ifrm = document.createElement("iframe");
                                ifrm.setAttribute("id", "payzone-iframe");
                                ifrm.setAttribute("name", "payzone-iframe");
                                ifrm.setAttribute("src", "{{ asset("assets/loading.html") }}");
                                ifrm.setAttribute("scrolling", "none");
                                ifrm.setAttribute("frameborder", "none");
                                var threeForm = document.createElement("form");
                                threeForm.setAttribute("id", "payzone_acs");
                                threeForm.setAttribute("name", "payzone_acs");
                                threeForm.setAttribute("action", responseObj["ACSURL"]);
                                threeForm.setAttribute("method", "POST");
                                threeForm.setAttribute("target", "payzone-iframe");
                                var MD = createInput('MD',responseObj["CrossReference"]);
                                var PaREQ = createInput('PaReq',responseObj["PaREQ"]);
                                var TermUrl = createInput('TermUrl',responseObj["TermUrl"]);
                                pzgModal.appendChild(ifrm);
                                pzgModal.appendChild(threeForm);
                                threeForm.appendChild(MD);
                                threeForm.appendChild(PaREQ);
                                threeForm.appendChild(TermUrl);
                                openPayzoneModal(5);
                                sizePayzoneModal('threed');
                                document.getElementById('payzone_acs').submit();
                                closeLoadingModal();
                            } else
                            {
                                closeLoadingModal();
                                sendToResults(responseObj);
                            }
                        }
                    };
                    XHR.send(cartData);
                }
                else {
                    closeLoadingModal();
                }
            }

            function createInput(name,value){
                input = document.createElement("input");
                input.setAttribute("name", name);
                input.setAttribute("type", "hidden");
                input.setAttribute("value", value);
                return input;
            }
        </script>
    @endif
@endsection
