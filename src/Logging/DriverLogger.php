<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Logging;

/**
 * Class DriverLogger
 *
 * Handles logging of driver operations to a file.
 */
class DriverLogger
{
    /**
     * @var string|null
     */
    protected $logPath;

    /**
     * DriverLogger constructor.
     *
     * @param string|null $logPath
     */
    public function __construct(string $logPath = null)
    {
        $this->logPath = $logPath;
    }

    /**
     * Log a message.
     *
     * @param string $driver
     * @param string $action
     * @param array $payload
     * @param bool $success
     * @param string|null $message
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
