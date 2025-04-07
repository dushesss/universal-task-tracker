<?php

declare(strict_types=1);

namespace UniversalTaskTracker;

use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use UniversalTaskTracker\Drivers\Bitrix24\Bitrix24Driver;
use UniversalTaskTracker\Drivers\Jira\JiraDriver;
use UniversalTaskTracker\Logging\DriverLogger;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class TrackerManager
 *
 * Manages the initialization and retrieval of a task tracker driver instance.
 * This class ensures a single driver is used throughout the application lifecycle.
 */
class TrackerManager
{
    /**
     * @var TrackerDriverInterface
     */
    protected $driver;

    /**
     * @var TrackerManager|null Singleton instance of the manager.
     */
    protected static $instance = null;

    /**
     * TrackerManager constructor.
     *
     * @param string $driverName
     * @param DriverLogger|null $logger
     */
    protected function __construct(string $driverName, DriverLogger $logger = null)
    {
        $this->driver = $this->resolveDriver($driverName, $logger);
    }

    /**
     * Initialize the tracker manager with a given driver.
     *
     * @param string $driverName
     * @param DriverLogger|null $logger
     * @return TrackerManager
     * @throws RuntimeException
     */
    public static function use(string $driverName, DriverLogger $logger = null)
    {
        if (self::$instance !== null) {
            throw new RuntimeException("Driver has already been set. Reinitialization is not allowed.");
        }

        self::$instance = new self($driverName, $logger);

        return self::$instance;
    }

    /**
     * Get the singleton instance of the TrackerManager.
     *
     * @return TrackerManager
     * @throws RuntimeException
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            throw new RuntimeException("Driver is not initialized. Call TrackerManager::use() first.");
        }

        return self::$instance;
    }

    /**
     * Get the currently active tracker driver instance.
     *
     * @return TrackerDriverInterface
     */
    public function driver()
    {
        return $this->driver;
    }

    /**
     * Resolve a driver name to its concrete implementation.
     *
     * @param string $driverName
     * @param DriverLogger|null $logger
     * @return TrackerDriverInterface
     */
    protected function resolveDriver(string $driverName, DriverLogger $logger = null)
    {
        switch (strtolower($driverName)) {
            case 'bitrix':
            case 'bitrix24':
                return new Bitrix24Driver($logger);

            case 'jira':
                return new JiraDriver($logger);

            default:
                throw new InvalidArgumentException("Unknown driver: {$driverName}");
        }
    }
}
