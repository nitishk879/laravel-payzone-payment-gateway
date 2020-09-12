<div class="card mb-3">
    <div class='card-header payzone-form-section'>
        <h4 class='payzone-form-header'>Shopping Cart Details</h4>
    </div>
    <div class='card-body payzone-form-section'>
        <div class="form-group mb-3">
            <label for='FullAmount'>FullAmount</label>
            <input type="number" class="form-control form-control-sm" id="FullAmount" name="FullAmount" value="{{ $session['FullAmount'] ?? $checkout['FullAmount'] ?? '' }}" />
        </div>
        <div class="form-group mb-3">
            <label for='OrderId'>OrderID</label>
            <input type="text" class="form-control form-control-sm" id="OrderId" name="OrderID" value="{{ $session['OrderID'] ?? $checkout['OrderID'] ?? '' }}" readonly/>
        </div>
        <div class="form-group">
            <label for='OrderDescription'>OrderDescription</label>
            <textarea name="OrderDescription" id="OrderDescription" class="form-control">{{ $checkout['OrderDescription'] ?? 'Buy a Cup of Coffee.' }}</textarea>
            <input type="hidden" name="TransactionDateTime" value="{{ $checkout['TransactionDateTime'] ??  today() }}" />
        </div>
    </div>
    <div class='payzone-form-section payzone-hidden'>
        @if($integrationType==\Payzone\Constants\INTEGRATION_TYPE::DIRECT)
            <input type="hidden" name="direct_submit" value="true" />
        @endif
    </div>
</div>
