<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Facades;

use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use UniversalTaskTracker\DTO\TrackerResponse;
use UniversalTaskTracker\Logging\DriverLogger;
use UniversalTaskTracker\TrackerManager;
use RuntimeException;

/**
 * TaskTracker
 *
 * Static facade for working with the current driver.
 * It can be used without Laravel.
 */
class TaskTracker
{
	/** @var TrackerManager|null */
	protected static $manager = null;

	/**
	 * Initialize the manager using a driver name.
	 *
	 * @param string $driverName Driver name like jira, bitrix, asana, clickup
	 * @param DriverLogger|null $logger Optional logger instance
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
	 * @param TrackerManager $manager Instance prepared outside
	 * @return void
	 */
	public static function setManager(TrackerManager $manager): void
	{
		self::$manager = $manager;
	}

	/**
	 * Create a task via current driver.
	 *
	 * @param array $data Task data (title, description, assignee, etc.)
	 * @return TrackerResponse Result with new id and other info
	 */
	public static function createTask(array $data): TrackerResponse
	{
		return self::driver()->createTask($data);
	}

	/**
	 * Update a task via current driver.
	 *
	 * @param string $taskId ID of the task to update
	 * @param array $data Fields to update
	 * @return TrackerResponse Result of update
	 */
	public static function updateTask(string $taskId, array $data): TrackerResponse
	{
		return self::driver()->updateTask($taskId, $data);
	}

	/**
	 * Delete a task via current driver.
	 *
	 * @param string $taskId ID of the task to delete
	 * @return TrackerResponse Result of delete
	 */
	public static function deleteTask(string $taskId): TrackerResponse
	{
		return self::driver()->deleteTask($taskId);
	}

	/**
	 * Get a task via current driver.
	 *
	 * @param string $taskId ID of the task to fetch
	 * @return TrackerResponse Result with task data
	 */
	public static function getTask(string $taskId): TrackerResponse
	{
		return self::driver()->getTask($taskId);
	}

	/**
	 * Get the underlying driver from the manager.
	 *
	 * @return TrackerDriverInterface Active driver instance
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
