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
  <p class='payzone-form-header'>Refund Details</p>
</div>
<div class='payzone-form-section'>
  <label for='FullAmount'>FullAmount</label>
  <input type="number" name="FullAmount" value="<?php echo (isset($_POST["FullAmount"]))?$_POST["FullAmount"]:null; ?>" />
  <label for='OrderId'>OrderID</label>
  <input type="text" name="OrderID" value="<?php echo (isset($_POST["OrderID"]))?$_POST["OrderID"]:null; ?>" />
  <label for='CrossReference'>CrossReference</label>
  <input type="text" name="CrossReference" value="<?php echo (isset($_POST["CrossReference"]))?$_POST["CrossReference"]:null; ?>" />
</div>
