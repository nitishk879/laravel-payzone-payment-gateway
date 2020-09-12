@extends('payzone::template')

@section('title', 'Cart- Payzone')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <form class='payzone-form' id='shopping-cart-form' name='shopping-cart-form' action="/payment" method='POST'>
                    @csrf
                    <div class="form-group">
                        <div class='payzone-form-section'>
                            <a href='https://www.payzone.co.uk/' target="_blank">
                                <img class='img-fluid payzone-logo' src="{{ asset("assets/images/payzone_logo.png") }}" alt="Payzone Payment" />
                            </a>
                        </div>
                    </div>
                    @if(Session::has('checkout'))
                        @php $checkout = Session::get('checkout'); @endphp
                        @include('payzone::components.cart-details')
                    @endif
                    @if($integrationType)
                        @include('payzone::components.customer-details')
                    @endif
                    <hr>
                    <span id='form_errors'></span>
                    <div class='payzone-form-section'>
                        <input id='payzone-cart-submit' type="submit" name="Submit" value="Submit"/>
                    </div>
                    <div class="form-group">
                        <div class='payzone-form-section'>
                            <a href="https://www.payzone.co.uk/" target="_blank">
                                <img class='img-fluid payzone-footer-image' src="{{ asset("assets/images/payzone_cards_accepted.png") }}" alt="card Accepted" />
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('model')
    @include('payzone::components.payzone-modal')
@endsection

@section('scripts')
    <script src="{{ asset("assets/payzone_validate.js") }}"></script>
    @if($integrationType)
        <script>
            document.getElementById("payzone-cart-submit").addEventListener("click", payzoneCartFormSubmission );
            function payzoneCartFormSubmission(evt){
                evt.preventDefault();
                var cartForm = document.getElementById('shopping-cart-form')
                var validated= validateForm(cartForm, 'direct','false');
                if(validated){
                    document.getElementById('shopping-cart-form').submit();
                }
            }
        </script>
    @endif
@endsection
