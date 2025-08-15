<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Contracts;

use UniversalTaskTracker\DTO\TrackerResponse;

/**
 * TrackerDriverInterface
 *
 * A common contract for task tracker drivers (Bitrix24, Jira, etc.).
 * Each driver should implement basic CRUD for tasks.
 */
interface TrackerDriverInterface
{
	/**
	 * Create a new task.
	 *
	 * @param array $data Task data like title, description, assignee, etc.
	 * @return TrackerResponse Result object with success flag and data (like new id)
	 */
	public function createTask(array $data): TrackerResponse;

	/**
	 * Update an existing task.
	 *
	 * @param string $taskId Task id to update
	 * @param array $data New values for fields
	 * @return TrackerResponse Result of update operation
	 */
	public function updateTask(string $taskId, array $data): TrackerResponse;

	/**
	 * Delete a task.
	 *
	 * @param string $taskId Task id to delete
	 * @return TrackerResponse Result of delete operation
	 */
	public function deleteTask(string $taskId): TrackerResponse;

	/**
	 * Get a task by id.
	 *
	 * @param string $taskId Task id to fetch
	 * @return TrackerResponse Result with task data or error
	 */
	public function getTask(string $taskId): TrackerResponse;
}
