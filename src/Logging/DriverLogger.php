<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Logging;

/**
 * DriverLogger
 *
 * Simple file logger for driver actions. If path is null, logging is off.
 */
class DriverLogger
{
	/**
	 * @var string|null Absolute path to log file, or null to disable
	 */
	protected $logPath;

	/**
	 * Create a logger.
	 *
	 * @param string|null $logPath Path to file. If null, it does not write logs.
	 */
	public function __construct(string $logPath = null)
	{
		$this->logPath = $logPath;
	}

	/**
	 * Write simple log line about an action.
	 *
	 * @param string $driver Driver name, like jira or bitrix
	 * @param string $action Short action name (createTask, getTask...)
	 * @param array $payload Context payload, will be json encoded
	 * @param bool $success If action was successful
	 * @param string|null $message Optional human message
	 * @return void
	 */
	public function log(string $driver, string $action, array $payload, bool $success = true, string $message = null)
	{
		if (!$this->logPath) {
			return;
		}

		$entry = sprintf(
			"[%s] [%s] ACTION: %s | SUCCESS: %s | MESSAGE: %s | PAYLOAD: %s\n",
			date('Y-m-d H:i:s'),
			strtoupper($driver),
			$action,
			$success ? 'YES' : 'NO',
			$message ?? '-',
			json_encode($payload)
		);

		@file_put_contents($this->logPath, $entry, FILE_APPEND);
	}
}
