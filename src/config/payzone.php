<?php

return [
    'payzone_merchant_id'       => env('PAYZONE_MERCHANT_ID' ?? 'Cellcr-2813986'),
    'payzone_merchant_pass'     => env('PAYZONE_MERCHANT_PASS' ?? 'Cellcrazyy1234'),
    'payzone_pre_shared_key'    => env('PAYZONE_PRE_SHARED_KEY' ?? 'E4aIg6C6dWwrTiuqBVFjV20MQE7Ck'),
    'payzone_currency'          => env('PAYZONE_CURRENCY_CODE' ?? 'GBP'),
    'payzone_currency_code'     => 826,

    'debug_status'      =>  false,
    'home-page'         => 'payzone',
    'cart-page'         => 'cart',
    'payment-page'      => 'payment',
    'process-payment'   => 'payment-process',
    'process-refund'    => 'refund',
    'result-page'       => 'callback-url',
    'loading-page'      => 'assets/loading.html',
    'form-action-payment'      => '',
    'response-form-handler'    => ''
];
