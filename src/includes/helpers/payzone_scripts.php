<?php
/**
* Payzone Payment Gateway
* ========================================
* Web:   http://payzone.co.uk
* Email:  online@payzone.com
* Authors: Payzone, Keith Rigby
*/

if (count(get_included_files()) ==1) {
    exit("Direct access not permitted.");
}

if($page=='payment' || $page=='cart' || $page=='results' || $page=='refund'){
  #Payzone Modal Form functions
  require_once(__DIR__ .'/../templates/payzone-modal.php');
}

if($page=='payment' || $page=='refund'){
?>
  <script>

  <?php
  if($page=='payment'){
  ?>
  PayzonePaymentForm = document.getElementById('payzone-payment-form');
  <?php
  }
  ?>
  <?php
  if($page=='refund'){
    ?>
    PayzonePaymentForm = document.getElementById('payzone-refund-form');
    <?php
  }
  ?>
    function payzonePaymentPageLoad(){
      <?php
      if ($PayzoneGateway->getIntegrationType() == \Payzone\Constants\INTEGRATION_TYPE::DIRECT ){ ?>
        PayzonePaymentForm.style.display='block';

        window.parent.postMessage({'option':'modalsize','value':'5'},"<?php echo $PayzoneHelper->getSiteSecureURL('root'); ?>");
        window.parent.postMessage({'option':'iframesrc','value':'payment'},"<?php echo $PayzoneHelper->getSiteSecureURL('root'); ?>");
      <?php
      } ?>
    }
  </script>
<?php
}

?>
<script src="assets/payzone_validate.js?v=1.0.8"></script>


<?php
### Direct API cart submission control
if($PayzoneGateway->getIntegrationType()==\Payzone\Constants\INTEGRATION_TYPE::DIRECT) {
  if($page=='cart'){
    ?>
      <script>
        document.getElementById("payzone-cart-submit").addEventListener("click",payzoneCartFormSubmission );
        function payzoneCartFormSubmission(evt){
          evt.preventDefault();
          var cartForm = document.getElementById('shopping-cart-form')
          var validated= validateForm(cartForm,'direct','false');
          if(validated){
            document.getElementById('shopping-cart-form').submit();
          }
        }
      </script>
    <?php
  }
  else if ($page=='payment'){
    ?>
    <script>
      var iframepage='payment';
      document.getElementById("payzone-payment-modal-background").addEventListener("click", closePayzoneModal);
      document.getElementById("payzone-modal-close").addEventListener("click", closePayzoneModal);
      document.getElementById("payzone-cart-submit").addEventListener("click", payzoneCartFormSubmission );
      document.getElementById("CrossReferenceTransaction").addEventListener("change", CrossReferenceTransaction);

      function CrossReferenceTransaction(evt){
        var CrossRefTrans = (document.getElementById("CrossReferenceTransaction").value == 'false') ? 'false': 'true';
        if (CrossRefTrans=='true'){
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
          XHR.open("POST", "<?php echo $PayzoneGateway->getURL('process-payment'); ?>?pzgact=process", true);
          XHR.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            responseObj = JSON.parse((this.responseText))
            if (responseObj["StatusCode"]==3){
              openPayzoneModal(1);
              var ifrm = document.createElement("iframe");
              ifrm.setAttribute("id", "payzone-iframe");
              ifrm.setAttribute("name", "payzone-iframe");
              ifrm.setAttribute("src", "<?php echo $PayzoneGateway->getURL('loading-page'); ?>");
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
    <?php
  }
  else if ($page=='refund-process' || $page=='refund'){
    ?>
    <script>
      var iframepage='refund';
      document.getElementById("payzone-payment-modal-background").addEventListener("click", closePayzoneModal);
      document.getElementById("payzone-modal-close").addEventListener("click", closePayzoneModal);
      document.getElementById("payzone-cart-submit").addEventListener("click", payzoneCartFormSubmission );
      function payzoneCartFormSubmission(evt){
        evt.preventDefault();
        window.addEventListener("message", receiveMessageCart, false);
        var cartForm = document.getElementById('payzone-refund-form');
        var pzgModal    = document.getElementById('payzone-payment-modal');
      //  openLoadingModal('loading');
        var validated = validateForm(cartForm,'refund','true');
        if (validated){
          var cartData = new FormData(cartForm);
          var refundAmount = document.getElementsByName('FullAmount')[0].value;
            var refundOrderId  = document.getElementsByName('OrderID')[0].value;
            var refundCrossRef = document.getElementsByName('CrossReference')[0].value;
          var XHR = new XMLHttpRequest();
          XHR.open("POST", "<?php echo $PayzoneGateway->getURL('process-payment'); ?>?pzgact=refund&pzgamt="+refundAmount+"&pzgorderid="+refundOrderId+"&pzgcrossref="+refundCrossRef, true);
          XHR.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
               console.log(this.responseText);
            responseObj = JSON.parse((this.responseText))
              closeLoadingModal();
              sendToResults(responseObj);
          }
        };
        XHR.send(cartData);
        }
        else {
          closeLoadingModal();
        }
      }
      </script>
    <?php
  }
}
?>
