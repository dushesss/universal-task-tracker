<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\Bitrix24;

use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use UniversalTaskTracker\DTO\TrackerResponse;
use UniversalTaskTracker\Logging\DriverLogger;

/**
 * Class Bitrix24Driver
 *
 * A Bitrix24 implementation of the TrackerDriverInterface.
 * Currently returns mocked responses. Future versions will integrate with Bitrix24 REST API.
 */
class Bitrix24Driver implements TrackerDriverInterface
{
    /**
     * @var DriverLogger|null
     */
    protected $logger;

    /**
     * Bitrix24Driver constructor.
     *
     * @param DriverLogger|null $logger Optional logger for tracking driver actions.
     */
    public function __construct(DriverLogger $logger = null)
    {
        $this->logger = $logger;
    }


    /**
     * Create a new task in Bitrix24.
     *
     * @param array $data Task data to be sent to Bitrix24.
     * @return TrackerResponse Contains success message and mock task ID.
     */
    public function createTask(array $data): TrackerResponse
    {
        return TrackerResponse::success('Task created in Bitrix24', ['id' => '123']);
    }

    /**
     * Update an existing task in Bitrix24.
     *
     * @param string $taskId ID of the Bitrix24 task to update.
     * @param array $data Updated data for the task.
     * @return TrackerResponse Contains update status.
     */
    public function updateTask(string $taskId, array $data): TrackerResponse
    {
        return TrackerResponse::success("Task $taskId updated in Bitrix24");
    }

    /**
     * Delete a task in Bitrix24.
     *
     * @param string $taskId ID of the Bitrix24 task to delete.
     * @return TrackerResponse Contains deletion status.
     */
    public function deleteTask(string $taskId): TrackerResponse
    {
        return TrackerResponse::success("Task $taskId deleted from Bitrix24");
    }

    /**
     * Retrieve a task from Bitrix24.
     *
     * @param string $taskId ID of the Bitrix24 task to retrieve.
     * @return TrackerResponse Contains task data or an error message.
     */
    public function getTask(string $taskId): TrackerResponse
    {
        return TrackerResponse::success("Task $taskId retrieved from Bitrix24", [
            'id' => $taskId,
            'title' => 'Sample task',
        ]);
    }
}
