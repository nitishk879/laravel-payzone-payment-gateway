<!DOCTYPE html>
<?php
require_once(__DIR__ . "/includes/payzone_gateway.php");
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payzone Gateway - Payment</title>
    <meta name="description" content="Payment Gateway example integration">
    <meta name="author" content="Keith Rigby - Payzone">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!--Payzone CSS -->
    <link rel="stylesheet" href="assets/payzone_gateway.css?v=1.3">
    <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
    <![endif]-->
</head>
<body onload="payzoneResultsOnload();" >
<div id='pzg-wrap'></div>
<?php



?>

<!--Payzone Scripts -->
<script>
    var iframepage='results-process';
    function payzoneResultsOnload(){

    }
</script>

<script>
    function createInput(name,value){
        input = document.createElement("input");
        input.setAttribute("name", name);
        input.setAttribute("type", "hidden");
        input.setAttribute("value", value);
        return input;
    }
</script>


<?php
$page='results';
require_once(__DIR__ . "/includes/helpers/payzone_scripts.php");
?>
</body>
</html>
