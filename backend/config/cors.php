<?php

// Lets the Vue dev server (a different origin) call this API from the browser.
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    // Vite auto-increments past 5173 if that port is already taken by another
    // project, so allow the next couple of ports out of the box too.
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173,http://localhost:5174,http://localhost:5175')),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
