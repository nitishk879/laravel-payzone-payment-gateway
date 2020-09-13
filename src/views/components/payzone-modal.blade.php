<div id='payzone-payment-modal-background'>
    <div id='payzone-payment-modal'>
        <a id='payzone-modal-close' href='#'>&times;</a>
    </div>
</div>


<div id='payzone-loading-modal-background'>
    <div id='payzone-loading-modal'>
        <div class='payzone-loading-wrap' id='payzone-loading-wrap'>
            <img src='{{ asset("assets/images/loading.gif") }}' alt="Loading...."/>
        </div>
    </div>
</div>
<script>
    var siteRoot = "{{ \Payzone\Helper\PayzoneHelper::getSiteSecureURL('root') }}";
    var siteBase = "{{ \Payzone\Helper\PayzoneHelper::getSiteSecureURL('base') }}";
    var cartPage = "/cart";
    var homePage = "/payzone";
    var pzgModal = document.getElementById('payzone-payment-modal');
    var pzgLoading = document.getElementById('payzone-loading-modal');
    var pzgModalBG = document.getElementById('payzone-payment-modal-background');
    var pzgLoadingBG = document.getElementById('payzone-loading-modal-background');
</script>
<script src="{{ asset("assets/payzone_modal.js?v=1.0.12") }}" data-autoinit="true"></script>
<script>
    document.getElementById("payzone-payment-modal-background").addEventListener("click", closePayzoneModal);
    document.getElementById("payzone-modal-close").addEventListener("click", closePayzoneModal);
</script>
