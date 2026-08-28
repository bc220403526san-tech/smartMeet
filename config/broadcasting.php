<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | Laravel/PHP sends Reverb events directly to the LOCAL Reverb server.
    | Browsers still connect publicly through VITE_REVERB_HOST / port 443.
    |
    */

    'default' => env('BROADCAST_CONNECTION', env('BROADCAST_DRIVER', 'reverb')),

    'connections' => [

        'reverb' => [
            'driver' => 'reverb',
            'key' => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'app_id' => env('REVERB_APP_ID'),

            'options' => [
                // IMPORTANT: server-to-server broadcasting stays inside the VM.
                'host' => env('REVERB_SERVER_HOST', '127.0.0.1'),
                'port' => (int) env('REVERB_SERVER_PORT', 8080),
                'scheme' => env('REVERB_SERVER_SCHEME', 'http'),
                'useTLS' => env('REVERB_SERVER_SCHEME', 'http') === 'https',
            ],

            'client_options' => [],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];
