<?php

declare(strict_types=1);

namespace UniversalTaskTracker;

use Illuminate\Support\ServiceProvider;

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
        // Bind the TrackerManager singleton into the container.
        $this->app->singleton(TrackerManager::class, function () {
            return TrackerManager::getInstance();
        });

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
        \UniversalTaskTracker\Facades\TaskTracker::use($driver);
    }
}
