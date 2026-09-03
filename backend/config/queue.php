<?php

return [
    // Nothing in this app is queued — kept as "sync" (runs immediately, inline)
    // only because Laravel's internals expect a queue config to exist.
    'default' => env('QUEUE_CONNECTION', 'sync'),

    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
    ],
];
