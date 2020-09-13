@extends('payzone::template')

@section('title', 'Success!')

@section('onload', "payzoneResultsOnload();")

@section('content')
    <div id='pzg-wrap'>

    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                @include('payzone::components.response')
            </div>
        </div>
    </div>
@endsection


@section('model')
    @include('payzone::components.payzone-modal')
@endsection


@section('scripts')
    <!--Payzone Scripts -->
    <script>
        var iframepage='results-process';
        function payzoneResultsOnload(){

        }
    </script>
    <script>
        var iframepage = 'results-process';
        function payzonePaymentPageLoad(){
            @if($payzoneGateway->getIntegrationType() == \Payzone\Constants\INTEGRATION_TYPE::DIRECT )
                PayzonePaymentForm.style.display='block';
                window.parent.postMessage({'option':'modalsize','value':'5'},"/");
                window.parent.postMessage({'option':'iframesrc','value':'payment'},"/");
            @endif
        }
    </script>

    <script>
        function createInput(name, value) {
            input = document.createElement("input");
            input.setAttribute("name", name);
            input.setAttribute("type", "hidden");
            input.setAttribute("value", value);
            return input;
        }
    </script>
@endsection
