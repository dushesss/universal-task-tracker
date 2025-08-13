<?php

declare(strict_types=1);

namespace UniversalTaskTracker;

use Illuminate\Support\ServiceProvider;
use UniversalTaskTracker\Logging\DriverLogger;
use UniversalTaskTracker\Core\TaskTrackerBuilder;
use UniversalTaskTracker\Core\Config\ArrayConfig;
use UniversalTaskTracker\Core\Registry\DriverRegistry;

/**
 * Class UniversalTaskTrackerServiceProvider
 *
 * Laravel service provider for the Universal Task Tracker package.
 */
class UniversalTaskTrackerServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     *
     * @return void
     */
    public function register()
    {
        // Optionally: register config
        $this->mergeConfigFrom(__DIR__ . '/../config/trackers.php', 'trackers');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the config file if the user runs artisan vendor:publish
        $this->publishes([
            __DIR__ . '/../config/trackers.php' => config_path('trackers.php'),
        ], 'task-tracker-config');

        $driver = config('trackers.driver');
        $logPath = config('trackers.log_path');

        $logger = new DriverLogger($logPath);

        // Build core manager instance via builder
        $config = new ArrayConfig(['trackers' => ['driver' => $driver, 'log_path' => $logPath]]);
        $registry = new DriverRegistry();
        // В данной итерации используем дефолтные драйверы через resolve внутри TrackerManager,
        // реестр можно использовать позже для внешней регистрации
        $builder = new TaskTrackerBuilder($config, $registry, $logger);
        $manager = $builder->build();

        \UniversalTaskTracker\Facades\TaskTracker::setManager($manager);
    }
}
