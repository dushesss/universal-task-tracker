<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\Jira;

use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use UniversalTaskTracker\DTO\TrackerResponse;

/**
 * Class JiraDriver
 *
 * A Jira implementation of the TrackerDriverInterface.
 * This class provides mocked responses for basic task operations.
 * Future versions may use Jira REST API to perform real operations.
 */
class JiraDriver implements TrackerDriverInterface
{
    /**
     * Create a new task in Jira.
     *
     * @param array $data Task data to be sent to Jira.
     * @return TrackerResponse Contains success message and mock task ID.
     */
    public function createTask(array $data): TrackerResponse
    {
        return TrackerResponse::success('Task created in Jira', ['id' => 'JIRA-456']);
    }

    /**
     * Update an existing task in Jira.
     *
     * @param string $taskId ID of the Jira task to update.
     * @param array $data Updated data for the task.
     * @return TrackerResponse Contains update status.
     */
    public function updateTask(string $taskId, array $data): TrackerResponse
    {
        return TrackerResponse::success("Task $taskId updated in Jira");
    }

    /**
     * Delete a task in Jira.
     *
     * @param string $taskId ID of the Jira task to delete.
     * @return TrackerResponse Contains deletion status.
     */
    public function deleteTask(string $taskId): TrackerResponse
    {
        return TrackerResponse::success("Task $taskId deleted from Jira");
    }

    /**
     * Retrieve a task from Jira.
     *
     * @param string $taskId ID of the Jira task to retrieve.
     * @return TrackerResponse Contains task data or an error message.
     */
    public function getTask(string $taskId): TrackerResponse
    {
        return TrackerResponse::success("Task $taskId retrieved from Jira", [
            'id' => $taskId,
            'title' => 'Sample Jira task',
        ]);
    }
}
