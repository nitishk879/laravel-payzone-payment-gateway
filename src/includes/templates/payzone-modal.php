<div id='payzone-payment-modal-background'></div>
<div id='payzone-payment-modal'>
    <a id='payzone-modal-close' href='#'>&times;</a>
</div>

<div id='payzone-loading-modal-background'></div>
<div id='payzone-loading-modal'>
    <div class='payzone-loading-wrap' id='payzone-loading-wrap'>
        <img src='assets/images/loading.gif'/>
    </div>
</div>
<script>
    var siteRoot = "<?php echo $PayzoneHelper->getSiteSecureURL('root'); ?>";
    var siteBase = "<?php echo $PayzoneHelper->getSiteSecureURL('base'); ?>";
    var cartPage = "<?php echo $PayzoneGateway->getURL('cart-page'); ?>";
    var homePage = "<?php echo $PayzoneGateway->getURL('home-page'); ?>";
    var pzgModal = document.getElementById('payzone-payment-modal');
    var pzgLoading = document.getElementById('payzone-loading-modal');
    var pzgModalBG = document.getElementById('payzone-payment-modal-background');
    var pzgLoadingBG = document.getElementById('payzone-loading-modal-background');
</script>
<script src="assets/payzone_modal.js?v=1.0.12" data-autoinit="true"></script>
<script>
    document.getElementById("payzone-payment-modal-background").addEventListener("click", closePayzoneModal);
    document.getElementById("payzone-modal-close").addEventListener("click", closePayzoneModal);
</script>
