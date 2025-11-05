<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fonnte API Token
    |--------------------------------------------------------------------------
    |
    | Token API Fonnte Anda. Dapatkan di https://fonnte.com
    |
    */
    'token' => env('FONNTE_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Fonnte API URL
    |--------------------------------------------------------------------------
    |
    | URL endpoint API Fonnte
    |
    */
    'api_url' => env('FONNTE_API_URL', 'https://api.fonnte.com/send'),
];