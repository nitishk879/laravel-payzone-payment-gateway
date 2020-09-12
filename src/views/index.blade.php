@extends('payzone::template')

@section('title', 'Payzone Payment gateway - home')

@section('onload', "submitForms();")

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h3>Example with data</h3>
            <form id='withDataForm' name='withDataForm' target="_self" action="/cart" method="POST" class=''>
                @csrf
                <p>To view the sample payment flow, prepopulated with post data (from this form) click on the below button, this will simulate the infomration being populated from your customer records.</p>
                <div class='form-row payzone-form-section'>
                    <div class="form-group col-md-6">
                        <label for='FullAmount'>FullAmount</label>
                        <input type="text" class="form-control" id="FullAmount" name="FullAmount" value="{{  $session['amount']  ?? ''}}" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for='OrderId'>OrderID</label>
                        <input type="text" class="form-control" id="OrderId" name="OrderID" value="{{  $session['order_id']  ?? ''}}" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for='TransactionDateTime'>TransactionDateTime</label>
                        <input type="text" class="form-control" id="TransactionDateTime" name="TransactionDateTime" value="{{ today() }}" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for='OrderDescription'>OrderDescription</label>
                        <input type="text" class="form-control" id="OrderDescription" name="OrderDescription" value="{{  $session['order_desc']  ?? ''}}" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for='CustomerName'>CustomerName</label>
                        <input type="text" class="form-control" id="CustomerName" name="CustomerName" value="{{  $session['customer_name']  ?? ''}}" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for='Address1'>Address1</label>
                        <input type="text" class="form-control" id="Address1" name="Address1" value="{{  $session['address_line1']  ?? ''}}" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for='Address2'>Address2</label>
                        <input type="text" class="form-control" id="Address2" name="Address2" value="{{  $session['address_line2']  ?? ''}}" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for='City'>City</label>
                        <input type="text" class="form-control" id="City" name="City" value="{{  $session['city']  ?? ''}}" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for='State'>State</label>
                        <input type="text" class="form-control" id="State" name="State" value="{{  $session['state']  ?? ''}}" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for='PostCode'>PostCode</label>
                        <input type="text" class="form-control" id="PostCode" name="PostCode" value="{{  $session['postal_code']  ?? ''}}" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for='Country'>Country</label>
                        <select name="Country" id="Country">
                            <option value="uk">Country</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <input type='submit'  target='_self' class='payzone-btn' value='Submit with data'/>
                </div>
            </form>
        </div>
        <div class="col-md-6">
            <div class='payzone-about-section payzone-configuration'>
                <h3>Example without data</h3>
                <form target="_self" action="/cart" method="POST" class=''>
                    @csrf
                    <div class='form-group payzone-form-section'>
                        <a href='https://www.payzone.co.uk/' target="_blank">
                            <img class='img-fluid' src="{{ asset("assets/images/payzone_logo.png") }}" alt="Payzone Payment Gateway"/>
                        </a>
                    </div>
                    <p>To view the sample payment flow, with no with post data being sent across (i.e. blank payment / new customer) please click the below button</p>
                    <input type='submit'  target='_self' class='payzone-btn' value='Submit without data'/>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        function submitForms()
        {
            withDataForm.submit()
        }
    </script>
@endsection
