<?php

return [
    'payzone_merchant_id'       => env('PAYZONE_MERCHANT_ID' ?? config('payzone.payzone_merchant_id')),
    'payzone_merchant_pass'     => env('PAYZONE_MERCHANT_PASS' ?? config('payzone.payzone_merchant_pass')),
    'payzone_pre_shared_key'    => env('PAYZONE_PRE_SHARED_KEY' ?? config('payzone.payzone_pre_shared_key')),
    'payzone_currency'          => env('PAYZONE_CURRENCY_CODE' ?? config('payzone.payzone_currency_code')),
    'payzone_currency_code'     => 826,

    'debug_status'      =>  false,
    'home-page'         => 'payzone',
    'cart-page'         => 'checkout',
    'payment-page'      => 'payment',
    'process-payment'   => 'payment-process',
    'process-refund'    => 'refund',
    'result-page'       => 'payment-success',
    'loading-page'      => 'assets/loading.html',
    'form-action-payment'      => '',
    'response-form-handler'    => ''
];
