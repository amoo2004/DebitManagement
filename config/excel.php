<?php

return [
    'exports' => [
        'csv' => [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => PHP_EOL,
            'use_bom' => false,
            'use_sheet_title' => false,
            'include_separator_line' => false,
            'excel_compatibility' => false,
            'output_encoding' => '',
            'test_auto_detect' => true,
        ],
        'xlsx' => [
            'use_sheet_title' => false,
        ],
    ],
    'imports' => [
        'csv' => [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape_controller' => '\\',
            'input_encoding' => 'UTF-8',
        ],
        'xlsx' => [
            'input_encoding' => 'UTF-8',
        ],
    ],
    'extension_detector' => [
        'xlsx' => 'Xlsx',
        'xls' => 'Xls',
        'csv' => 'Csv',
        'html' => 'HTML',
    ],
    'value_binder' => [
        'default' => Maatwebsite\Excel\DefaultValueBinder::class,
    ],
    'cache' => [
        'driver' => 'memory',
        'settings' => [
            'memory_cache_size' => '256MB',
        ],
    ],
    'transactions' => [
        'handler' => 'db',
    ],
    'temporary_files' => [
        'local_path' => storage_path('framework/cache/laravel-excel'),
        'remote_disk' => null,
        'remote_prefix' => null,
    ],
];
