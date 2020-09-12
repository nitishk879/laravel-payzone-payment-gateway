<?php /**
* Payzone Payment Gateway
* ========================================
* Web:   http://payzone.co.uk
* Email:  online@payzone.com
* Authors: Payzone, Keith Rigby
*/

if (count(get_included_files()) ==1) {
    exit("Direct access not permitted.");
}

?>
<div class='payzone-form-section'>
  <p class='payzone-form-header'>Shopping Cart Details</p>
</div>
<div class='payzone-form-section'>
  <label for='FullAmount'>FullAmount</label>
  <input type="number" name="FullAmount" value="<?php echo (isset($_POST["FullAmount"]))?$_POST["FullAmount"]:null; ?>" />
  <label for='OrderId'>OrderID</label>
  <input type="text" name="OrderID" value="<?php echo (isset($_POST["OrderID"]))?$_POST["OrderID"]:null; ?>" />
  <label for='OrderDescription'>OrderDescription</label>
  <textarea name="OrderDescription" ><?php echo (isset($_POST["OrderDescription"])) ?$_POST["OrderDescription"]:null; ?> </textarea>
  <input type="hidden" name="TransactionDateTime" value="<?php echo (isset($_POST["TransactionDateTime"]))?$_POST["TransactionDateTime"]: date('Y-m-d H:i:s P'); ?>" />
</div>
<div class='payzone-form-section payzone-hidden'>
  <?php
  if ($PayzoneGateway->getIntegrationType() == \Payzone\Constants\INTEGRATION_TYPE::DIRECT) {
    ?>
    <input type="hidden" name="direct_submit" value="true" />
    <?php
  }
  ?>
</div>
