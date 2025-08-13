<?php

declare(strict_types=1);

namespace UniversalTaskTracker;

use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use UniversalTaskTracker\Drivers\Bitrix24\Bitrix24Driver;
use UniversalTaskTracker\Drivers\Jira\JiraDriver;
use UniversalTaskTracker\Logging\DriverLogger;
use InvalidArgumentException;
use RuntimeException;
use UniversalTaskTracker\Core\Registry\DriverRegistry;
use UniversalTaskTracker\DTO\TrackerResponse;

/**
 * TrackerManager is a thin wrapper around a concrete TrackerDriverInterface.
 *
 * It supports two usage patterns:
 * - Singleton-style static initialization via use()/getInstance()
 * - Instance-style via create() for framework-agnostic DI
 */
class TrackerManager
{
	/**
	 * @var TrackerDriverInterface
	 */
	protected $driver;

	/**
	 * @var TrackerManager|null Global singleton instance.
	 */
	protected static $instance = null;

	/**
	 * @var DriverRegistry|null Optional registry for resolving drivers by name.
	 */
	protected $registry;

	/**
	 * @param string $driverName Name of the driver (e.g., bitrix, jira)
	 * @param DriverLogger|null $logger Optional logger passed to the driver
	 * @param DriverRegistry|null $registry Optional registry to resolve the driver
	 */
	protected function __construct(string $driverName, DriverLogger $logger = null, DriverRegistry $registry = null)
	{
		$this->registry = $registry;
		$this->driver = $this->resolveDriver($driverName, $logger);
	}

	/**
	 * Factory method to create a new manager instance without touching the singleton.
	 */
	public static function create(string $driverName, DriverLogger $logger = null, DriverRegistry $registry = null): TrackerManager
	{
		return new self($driverName, $logger, $registry);
	}

	/**
	 * Initialize a global singleton manager instance.
	 * Throws if called more than once.
	 */
	public static function use(string $driverName, DriverLogger $logger = null, DriverRegistry $registry = null)
	{
		if (self::$instance !== null) {
			throw new RuntimeException("Driver has already been set. Reinitialization is not allowed.");
		}

		self::$instance = new self($driverName, $logger, $registry);

		return self::$instance;
	}

	/**
	 * Get the global singleton instance.
	 */
	public static function getInstance()
	{
		if (self::$instance === null) {
			throw new RuntimeException("Driver is not initialized. Call TrackerManager::use() first.");
		}

		return self::$instance;
	}

	/**
	 * Get the underlying driver.
	 */
	public function driver()
	{
		return $this->driver;
	}

	/** Create a new task via the underlying driver. */
	public function createTask(array $data): TrackerResponse
	{
		return $this->driver->createTask($data);
	}

	/** Update a task via the underlying driver. */
	public function updateTask(string $taskId, array $data): TrackerResponse
	{
		return $this->driver->updateTask($taskId, $data);
	}

	/** Delete a task via the underlying driver. */
	public function deleteTask(string $taskId): TrackerResponse
	{
		return $this->driver->deleteTask($taskId);
	}

	/** Get a task via the underlying driver. */
	public function getTask(string $taskId): TrackerResponse
	{
		return $this->driver->getTask($taskId);
	}

	/**
	 * Resolve a driver by name either through the registry or default mapping.
	 */
	protected function resolveDriver(string $driverName, DriverLogger $logger = null)
	{
		if ($this->registry instanceof DriverRegistry) {
			return $this->registry->make($driverName);
		}

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
