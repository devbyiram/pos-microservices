<?php
return [
    'paths' => ['*'], // or ['api/*', 'auth/*'] depending on your routes
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:8003', 'http://127.0.0.1:8003'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // If you're using cookies (like you are)
];
