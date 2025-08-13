<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Facades;

use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use UniversalTaskTracker\DTO\TrackerResponse;
use UniversalTaskTracker\Logging\DriverLogger;
use UniversalTaskTracker\TrackerManager;
use RuntimeException;

/**
 * Class TaskTracker
 *
 * Universal static interface for interacting with a selected task tracker driver.
 * This is a standalone facade and does not require Laravel.
 */
class TaskTracker
{
    /**
     * @var TrackerManager|null
     */
    protected static $manager = null;

    /**
     * @param string $driverName
     * @param DriverLogger|null $logger
     * @return void
     */
    public static function use(string $driverName, DriverLogger $logger = null)
    {
        if (self::$manager !== null) {
            throw new \RuntimeException("Driver has already been initialized.");
        }

        self::$manager = TrackerManager::use($driverName, $logger);
    }

    /**
     * Set a prepared manager instance.
     *
     * @param TrackerManager $manager
     * @return void
     */
    public static function setManager(TrackerManager $manager): void
    {
        self::$manager = $manager;
    }

    /**
     * Create a new task using the current tracker driver.
     *
     * @param array $data
     * @return TrackerResponse
     */
    public static function createTask(array $data): TrackerResponse
    {
        return self::driver()->createTask($data);
    }

    /**
     * Update a task using the current tracker driver.
     *
     * @param string $taskId
     * @param array $data
     * @return TrackerResponse
     */
    public static function updateTask(string $taskId, array $data): TrackerResponse
    {
        return self::driver()->updateTask($taskId, $data);
    }

    /**
     * Delete a task using the current tracker driver.
     *
     * @param string $taskId
     * @return TrackerResponse
     */
    public static function deleteTask(string $taskId): TrackerResponse
    {
        return self::driver()->deleteTask($taskId);
    }

    /**
     * Retrieve a task using the current tracker driver.
     *
     * @param string $taskId
     * @return TrackerResponse
     */
    public static function getTask(string $taskId): TrackerResponse
    {
        return self::driver()->getTask($taskId);
    }

    /**
     * Get the underlying driver from the manager.
     *
     * @return TrackerDriverInterface
     * @throws RuntimeException If the driver was not initialized.
     */
    protected static function driver(): TrackerDriverInterface
    {
        if (!self::$manager) {
            throw new RuntimeException("Driver not initialized. Call TaskTracker::use() first.");
        }

        return self::$manager->driver();
    }
}
