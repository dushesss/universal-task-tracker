<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Tracker Driver
    |--------------------------------------------------------------------------
    |
    | This option controls which driver should be used by default when using
    | the Universal Task Tracker package inside a Laravel application.
    | You may override this value via the TRACKER_DRIVER environment variable.
    |
    */

    'driver' => env('TRACKER_DRIVER', 'bitrix'),

    /*
    |--------------------------------------------------------------------------
    | Tracker Driver Logging
    |--------------------------------------------------------------------------
    |
    | Path where all tracker operations will be logged. If null, logging is disabled.
    | Example: storage_path('logs/task-tracker.log')
    |
    */

    'log_path' => env('TRACKER_LOG_PATH', __DIR__ . '/../storage/logs/task-tracker.log'),
];
