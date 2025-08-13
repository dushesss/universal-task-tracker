<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Bridge\Laravel;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use UniversalTaskTracker\Logging\DriverLogger;
use UniversalTaskTracker\Core\TaskTrackerBuilder;
use UniversalTaskTracker\Core\Config\ArrayConfig;
use UniversalTaskTracker\Core\Registry\DriverRegistry;
use UniversalTaskTracker\Facades\TaskTracker as StaticTaskTracker;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/trackers.php', 'trackers');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/trackers.php' => config_path('trackers.php'),
        ], 'task-tracker-config');

        $driver = config('trackers.driver');
        $logPath = config('trackers.log_path');

        $logger = new DriverLogger($logPath);
        $config = new ArrayConfig(['trackers' => ['driver' => $driver, 'log_path' => $logPath]]);
        $registry = new DriverRegistry();
        $builder = new TaskTrackerBuilder($config, $registry, $logger);
        $manager = $builder->build();

        StaticTaskTracker::setManager($manager);
    }
} 