<?php

return [

    'default' => env('BROADCAST_CONNECTION', env('BROADCAST_DRIVER', 'reverb')),

    'connections' => [

        'reverb' => [
            'driver' => 'reverb',
            'key' => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'app_id' => env('REVERB_APP_ID'),

            /*
             * IMPORTANT:
             * REVERB_HOST / PORT / SCHEME are where Laravel sends broadcasts.
             * Apache proxies /apps/ to the local Reverb process.
             */
            'options' => [
                'host' => env('REVERB_HOST', 'smartmeet.live'),
                'port' => (int) env('REVERB_PORT', 443),
                'scheme' => env('REVERB_SCHEME', 'https'),
                'useTLS' => env('REVERB_SCHEME', 'https') === 'https',
            ],

            'client_options' => [
                'timeout' => 10,
                'connect_timeout' => 5,
            ],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];
