<?php

// This API has no login and no session-based state — sessions aren't used
// anywhere in the app. This file exists only because a couple of framework
// internals expect config('session.*') to be readable.
return [
    'driver' => env('SESSION_DRIVER', 'array'),
    'lifetime' => (int) env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'files' => storage_path('framework/sessions'),
    'cookie' => 'photography_session',
    'path' => '/',
    'domain' => null,
    'secure' => null,
    'http_only' => true,
    'same_site' => 'lax',
];
