@extends('payzone::template')

@section('title', 'Failed!')

@section('onload', "payzoneResultsOnload();")

@section('content')
    <div id='pzg-wrap'></div>
@endsection

@section('scripts')
    <!--Payzone Scripts -->
    <script>
        var iframepage = 'results-process';

        function payzoneResultsOnload() {

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

@include('payzone::components.payzone-modal')
