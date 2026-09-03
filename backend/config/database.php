<?php

return [
    // SQLite by default — running locally needs no database server at all,
    // just a file. Switch to "pgsql" (see below) for the Docker/Postgres setup.
    'default' => env('DB_CONNECTION', 'sqlite'),

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            // A real file for local dev (database/database.sqlite); phpunit.xml
            // overrides this to ":memory:" so the test suite never touches it.
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],

        // Used by the Docker Compose setup (docker-compose.yml sets
        // DB_CONNECTION=pgsql explicitly), or any time you want Postgres
        // instead of SQLite locally.
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'photography'),
            'username' => env('DB_USERNAME', 'photography'),
            'password' => env('DB_PASSWORD', 'photography'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],
];
