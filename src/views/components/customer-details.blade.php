<div class="card">
    <div class="card-header">
        <div class='payzone-form-section'>
            <h3 class='payzone-form-header'>Customer Details</h3>
        </div>
    </div>
    <div class="card-body">
        <div class='form-row payzone-form-section'>
            <div class="col-md-12 mb-3">
                <label for='CustomerName'>CustomerName</label>
                <input type="text" id="CustomerName" class="form-control form-control-sm" name="CustomerName" value="Geoff Wayne" />
            </div>
            <div class="col-md-12 mb-3">
                <label for='Address1'>Address1</label>
                <input type="text" id="Address1" class="form-control form-control-sm" name="Address1" value="113 Glendower Road" />
            </div>
            <div class="col-md-12 mb-3">
                <label for='Address2'>Address2</label>
                <input type="text" id="Address2" class="form-control form-control-sm" name="Address2" value="{{ old("Address2") ?? '' }}" />
            </div>
            <div class="col-md-12 mb-3">
                <label for='City'>City</label>
                <input type="text" id="City" class="form-control form-control-sm" name="City" value="Birmingham" />
            </div>
            <div class="col-md-12 mb-3">
                <label for='State'>State</label>
                <input type="text" id="State" class="form-control form-control-sm" name="State" value="West Midlands" />
            </div>
            <div class="col-md-12 mb-3">
                <label for='EmailAddress'>EmailAddress</label>
                <input type="text" id="EmailAddress" class="form-control form-control-sm" name="EmailAddress" value="contact@geoffwayne.com" />
            </div>
            <div class="col-md-12 mb-3">
                <label for='PostCode'>PostCode</label>
                <input type="text" id="PostCode" class="form-control form-control-sm" name="PostCode" value="B42 1SX" />
            </div>
            <div class="col-md-12 mb-3">
                <label for='Country'>Country</label>
                <select id="Country" class="custom-select" name="Country">
                    <option value="">Country</option>
                    <?php echo \Payzone\Helper\PayzoneHelper::getCountryDropDownlist(($country ?? null));?>
                </select>
            </div>
        </div>
    </div>
</div>
