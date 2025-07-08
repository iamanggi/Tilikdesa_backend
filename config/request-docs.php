<?php

return [
    // Judul dokumen
    'title' => 'TilikDesa API Docs',
    'enabled' => true,
    'debug' => false,

    /*
     * URL akses dokumentasi (http://localhost:8000/request-docs)
     */
    'url' => 'request-docs',

    // Middleware hanya aktif di development
    'middlewares' => [
         \Rakutentech\LaravelRequestDocs\NotFoundWhenProduction::class,
    ],

    // Hanya generate dokumentasi untuk route yang diawali dengan /api
    'only_route_uri_start_with' => '',

    'hide_matching' => [
        '#^telescope#',
        '#^docs#',
        '#^request-docs#',
        '#^api-docs#',
        '#^sanctum#',
        '#^_ignition#',
        '#^_tt#',
        '#^up#',
        '#^/#',
        '#^storage/{path}#',
    ],

    'hide_meta_data' => false,
    'hide_sql_data' => false,
    'hide_logs_data' => false,
    'hide_models_data' => false,

    // Method validasi yang didukung
    'rules_methods' => [
        'rules'
    ],

    // Response default untuk dokumentasi
    'default_responses' => ["200", "400", "401", "403", "404", "405", "422", "429", "500", "503"],

    // Header default semua request
    'default_headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ],

    // Grouping endpoint berdasarkan pola URI
    'group_by' => [
        'uri_patterns' => [
            '^api/v[\d]+/',
            '^api/',
        ]
    ],

    // Konfigurasi OpenAPI (jika ingin export ke format JSON OpenAPI)
    'open_api' => [
        'title' => 'TilikDesa API Documentation',
        'description' => 'Dokumentasi endpoint API TilikDesa untuk pelaporan kerusakan fasilitas publik.',
        'version' => '3.0.0',
        'document_version' => '1.0.0',
        'license' => 'Apache 2.0',
        'license_url' => 'https://www.apache.org/licenses/LICENSE-2.0.html',
        'server_url' => env('APP_URL', 'http://localhost:8000'),
        'delete_with_body' => false,
        'exclude_http_methods' => [],
        'responses' => [
            '200' => [
                'description' => 'Successful operation',
                'content' => [
                    'application/json' => ['schema' => ['type' => 'object']],
                ],
            ],
            '400' => ['description' => 'Bad Request', 'content' => ['application/json' => ['schema' => ['type' => 'object']]]],
            '401' => ['description' => 'Unauthorized', 'content' => ['application/json' => ['schema' => ['type' => 'object']]]],
            '403' => ['description' => 'Forbidden', 'content' => ['application/json' => ['schema' => ['type' => 'object']]]],
            '404' => ['description' => 'Not Found', 'content' => ['application/json' => ['schema' => ['type' => 'object']]]],
            '422' => ['description' => 'Unprocessable Entity', 'content' => ['application/json' => ['schema' => ['type' => 'object']]]],
            '500' => ['description' => 'Internal Server Error', 'content' => ['application/json' => ['schema' => ['type' => 'object']]]],
            'default' => ['description' => 'Unexpected error', 'content' => ['application/json' => ['schema' => ['type' => 'object']]]],
        ],

        // Gunakan bearer token di header Authorization
        'security' => [
            'type' => 'bearer',  // cocok untuk Sanctum / JWT
            'name' => 'Authorization',
            'position' => 'header',
        ],
    ],

    // Path export ke json (opsional)
    'export_path' => 'api.json',
];
