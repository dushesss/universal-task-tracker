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

    'driver' => (function () {
        $value = getenv('TRACKER_DRIVER');
        return $value !== false ? $value : 'bitrix';
    })(),

    /*
    |--------------------------------------------------------------------------
    | Tracker Driver Logging
    |--------------------------------------------------------------------------
    |
    | Path where all tracker operations will be logged. If null, logging is disabled.
    | Example: storage_path('logs/task-tracker.log')
    |
    */

    'log_path' => (function () {
        $value = getenv('TRACKER_LOG_PATH');
        return $value !== false ? $value : (__DIR__ . '/../storage/logs/task-tracker.log');
    })(),
];
