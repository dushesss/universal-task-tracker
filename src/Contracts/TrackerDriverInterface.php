<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Contracts;

use UniversalTaskTracker\DTO\TrackerResponse;

/**
 * Interface TrackerDriverInterface
 *
 * Defines a unified contract for all task tracker drivers (e.g., Bitrix24, Jira).
 * Each driver must implement the following basic task management operations.
 */
interface TrackerDriverInterface
{
    /**
     * Create a new task in the tracker.
     *
     * @param array $data Task data, including title, description, assignee, etc.
     * @return TrackerResponse Contains information about the result of the creation.
     */
    public function createTask(array $data): TrackerResponse;

    /**
     * Update an existing task.
     *
     * @param string $taskId ID of the task to update.
     * @param array $data Updated task data.
     * @return TrackerResponse Contains the result of the update operation.
     */
    public function updateTask(string $taskId, array $data): TrackerResponse;

    /**
     * Delete a task from the tracker.
     *
     * @param string $taskId ID of the task to delete.
     * @return TrackerResponse Contains the result of the delete operation.
     */
    public function deleteTask(string $taskId): TrackerResponse;

    /**
     * Retrieve a task from the tracker.
     *
     * @param string $taskId ID of the task to retrieve.
     * @return TrackerResponse Contains the task data or an error message.
     */
    public function getTask(string $taskId): TrackerResponse;
}
