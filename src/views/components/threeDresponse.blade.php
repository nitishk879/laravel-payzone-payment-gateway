<script>
    window.parent.postMessage({'option':'iframesrc','value':'three-response'},"/");
    window.parent.postMessage({'option':'threedresponse','value':"{!! json_encode($paymentResponse) !!}"},"/");
</script>
