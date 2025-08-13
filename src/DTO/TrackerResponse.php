<?php

declare(strict_types=1);

namespace UniversalTaskTracker\DTO;

/**
 * Standardized response structure returned by all drivers.
 */
class TrackerResponse
{
	/**
	 * Indicates whether the operation succeeded.
	 * @var bool
	 */
	public $success;

	/**
	 * Human-readable message describing the outcome.
	 * @var string
	 */
	public $message;

	/**
	 * Optional payload with additional data (e.g., created task id, raw response).
	 * @var array|null
	 */
	public $data = null;

	/**
	 * @param bool $success
	 * @param string $message
	 * @param array|null $data
	 */
	public function __construct(bool $success, string $message, array $data = null)
	{
		$this->success = $success;
		$this->message = $message;
		$this->data = $data;
	}

	/** Create a successful response. */
	public static function success(string $message, array $data = null): TrackerResponse
	{
		return new self(true, $message, $data);
	}

	/** Create an error response. */
	public static function error(string $message): TrackerResponse
	{
		return new self(false, $message);
	}
}
