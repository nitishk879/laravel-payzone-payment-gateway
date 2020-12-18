<?php

return [
    'payzone_merchant_id'       => env('PAYZONE_MERCHANT_ID' ?? 'something-1234567'),
    'payzone_merchant_pass'     => env('PAYZONE_MERCHANT_PASS' ?? 'pass123e4'),
    'payzone_pre_shared_key'    => env('PAYZONE_PRE_SHARED_KEY' ?? 'aladskjfj8732jakf'),
    'payzone_currency'          => env('PAYZONE_CURRENCY_CODE' ?? 'GBP'),
    'payzone_currency_code'     => 826,

    'debug_status'      =>  false,
    'home-page'         => 'payzone',
    'cart-page'         => 'cart',
    'payment-page'      => 'payment',
    'process-payment'   => 'payment-process',
    'process-refund'    => 'refund',
    'result-page'       => 'payment-success',
    'loading-page'      => 'assets/loading.html',
    'form-action-payment'      => '',
    'response-form-handler'    => ''
];
