<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Di sinilah Anda dapat mengonfigurasi pengaturan untuk cross-origin
    | resource sharing atau "CORS".
    |
    */

    // config/cors.php
'paths' => ['api/*'], // Pastikan ada 'api/*'

'allowed_methods' => ['*'], // 'POST', 'GET', 'OPTIONS', dll.

'allowed_origins' => [
    'http://localhost:3000', // Frontend React Anda
],

'allowed_headers' => ['*'], // Izinkan semua header

'supports_credentials' => true, // WAJIB 'true' untuk login

];