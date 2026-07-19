<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fonnte API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Fonnte API settings. You can get your
    | token from the Fonnte dashboard.
    |
    */

    'token' => env('FONNTE_TOKEN'),
    
    'base_url' => env('FONNTE_BASE_URL', 'https://api.fonnte.com'),

    'sender' => env('WA_SENDER_NUMBER'), // Optional
];
