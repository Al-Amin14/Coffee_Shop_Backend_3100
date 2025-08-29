<?php

return [

    'paths' => ['api/*'],            // apply CORS only to API routes
    'allowed_methods' => ['*'],      // allow GET, POST, etc.
    'allowed_origins' => ['*'], // React dev server
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
