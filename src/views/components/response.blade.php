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

if ($showresults){
if($validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::SUCCESS || $validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::DECLINED || $validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::DUPLICATE || $validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::THREED || $validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::ERROR || $validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::UNKNOWN ){?>
<div class='payzone-transaction-results'>
    <div class='payzone-results-section pz-left'>
        <a href='https://www.payzone.co.uk/' target="_blank">
            <img alt="" class='payzone-logo' src="{{ asset("/assets/images/payzone_logo.png") }}" />
        </a>
    </div>
    <div class='payzone-results-section <?php echo $validate["Notification"]["Class"];?>'>
        <p class='payzone-results-header'>Transaction Status</p>
        <?php
        if ($validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::DUPLICATE) { ?>
        <p class='payzone-results-sub-header'><?php echo $validate["Notification"]["Title"] ;?> </p>
        <p><?php echo $validate["Notification"]["Message"] ;?> </p>
        <p class='payzone-results-details'>Previous Response: <?php echo $validate["Response"]["PreviousResponse"]["PreviousMessage"];?></p>
        <?php
        }
        else { ?>
        <p class='payzone-results-sub-header'><?php echo $validate["Notification"]["Title"] ;?> </p>
        <p><?php echo $validate["Notification"]["Message"] ;?> </p>
        <p class='payzone-results-details'><?php echo $validate["Response"]["Message"];?></p>
        <?php if($validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::ERROR || $validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::UNKNOWN){ ?>

        <p class='payzone-results-details'><?php echo $validate["Response"]["ErrorMessages"]; ?></p>
        <?php } ?>
        <?php
        } ?>
    </div>
    <?php
    if ( $paymentGateway->getOrderDetails() && !($validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::THREED)) { ?>
    <hr>
    <div class='payzone-results-section'>
        <p class='payzone-results-sub-header'>Order Details</p>
        <p class='payzone-results-details'>Order ID: <?php echo $validate["Order"]["OrderID"]; ?></p>
        <p class='payzone-results-details'>Amount: <?php echo $validate["Order"]["Amount"]; ?>
            <?php echo ($validate["Order"]["TransactionType"]==\Payzone\Constants\TRANSACTION_TYPE::PREAUTH && $validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::SUCCESS  ) ? " (Pre-authorised)": ""; ?>
        </p>
        @if($validate['Notification']['refund'] ?? '')
            <?php if($validate["Notification"]["Refund"]!="Refund"){ ?>
            <p class='payzone-results-details'>Description: <?php echo $validate["Order"]["OrderDescription"]; ?></p>
            <?php } ?>
        @endif
    </div>
    <?php
    } ?>
    <hr>
    <a target="_top" href='/'>Return to home page</a>
    <hr>
    <div class='payzone-results-section'>
        <a href='https://www.payzone.co.uk/' target="_blank">
            <img alt="" class='payzone-logo-footer' src="{{ asset("/assets/images/payzone_secure_badge.png") }}" />
        </a>
    </div>
</div>
<?php
}
else if($validate["Notification"]["Type"]==\Payzone\Constants\PAYZONE_RESPONSE_OUTCOMES::ERROR){?>
<div class='payzone-transaction-results'>
    <div class='payzone-results-section pz-left'>
        <a href='https://www.payzone.co.uk/' target="_blank">
            <img alt="" class='payzone-logo' src="{{ asset("/assets/images/payzone_logo.png") }}" />
        </a>
    </div>
    <div class='payzone-results-section'>
        <p class='payzone-results-header'>Unable to process transaction</p>
        <p class='payzone-results-details'>Gateway Response:</p>
        <p class='payzone-results-details'><?php echo $validate["Notification"]["Message"]; ?></p>
    </div>
    <hr>
    <a target="_top" href='/'>Return to home page</a>
        <hr>
        <div class='payzone-results-section'>
            <a href='https://www.payzone.co.uk/' target="_blank">
                <img alt="" class='payzone-logo-footer' src="{{ asset("/assets/images/payzone_secure_badge.png") }}" />
            </a>
        </div>
</div>
<?php
}
} ?>
