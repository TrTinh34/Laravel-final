<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Các cổng thanh toán mặc định
    |--------------------------------------------------------------------------
    */
    'gateways' => [
        'VNPay' => [
            'driver' => 'VNPay',
            'options' => [
                'vnp_TmnCode' => env('VNPAY_TMN_CODE'),
                'vnp_HashSecret' => env('VNPAY_HASH_SECRET'),
                'vnp_Url' => env('VNPAY_URL'),
            ]
        ]
    ]
];