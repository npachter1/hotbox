<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pincode Default
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'pincode' => [
        'length' => 4
    ],

    'metrc' => [
        '403-X0001' => [
            'url' => env('METRC_403_X0001_URL'),
            'key' => env('METRC_403_X0001_KEY')
        ],
        '402R-00132' => [
            'url' => env('METRC_402R_00132_URL'),
            'key' => env('METRC_402R_00132_KEY')
        ],
        '500-X0001' => [
            'url' => env('METRC_500_X0001_URL'),
            'key' => env('METRC_500_X0001_KEY')
        ],
        '020-X0002' => [
            'url' => env('METRC_020_X0002_URL'),
            'key' => env('METRC_020_X0002_KEY')
        ],
        '403-01009' => [
            'url' => env('METRC_403_01009_URL'),
            'key' => env('METRC_403_01009_KEY')
        ]
    ],
    
    'discount_rules' => [
        'min_per_line' => 0.01
    ],

    'hash_secret' => env('HOTBOX_HASH_SECRET'),

    'pos_url' => env('HOTBOX_POS_URL')
];
