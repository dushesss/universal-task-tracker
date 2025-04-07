<?php

declare(strict_types=1);

namespace UniversalTaskTracker\DTO;

/**
 * Class TrackerResponse
 *
 * A standardized response structure for task tracker operations.
 * Used as a return type from all drivers (e.g., Bitrix24, Jira).
 */
class TrackerResponse
{
    /**
     * @var bool Indicates if the operation was successful.
     */
    public $success;

    /**
     * @var string A human-readable message about the operation result.
     */
    public $message;

    /**
     * @var array|null Additional data related to the response (e.g., task ID, details).
     */
    public $data = null;

    /**
     * TrackerResponse constructor.
     *
     * @param bool $success Whether the operation succeeded.
     * @param string $message A message describing the result.
     * @param array|null $data Optional additional data.
     */
    public function __construct(bool $success, string $message, array $data = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Create a successful response.
     *
     * @param string $message A success message.
     * @param array|null $data Optional data.
     * @return self
     */
    public static function success(string $message, array $data = null): TrackerResponse
    {
        return new self(true, $message, $data);
    }

    /**
     * Create an error response.
     *
     * @param string $message An error message.
     * @return self
     */
    public static function error(string $message): TrackerResponse
    {
        return new self(false, $message);
    }
}
