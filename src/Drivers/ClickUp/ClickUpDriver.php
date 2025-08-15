<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\ClickUp;

use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use UniversalTaskTracker\DTO\TrackerResponse;
use UniversalTaskTracker\DTO\Task;
use UniversalTaskTracker\Http\Contracts\HttpClientInterface;
use UniversalTaskTracker\Logging\DriverLogger;
use UniversalTaskTracker\Contracts\ClickUpConnectionInterface;

final class ClickUpDriver implements TrackerDriverInterface
{
	private $logger;
	private $http;
	private $conn;
	private $listId; // required to create task

	/**
	 * Create ClickUp driver instance.
	 *
	 * @param DriverLogger|null $logger Optional logger
	 * @param HttpClientInterface|null $http PSR-based HTTP client
	 * @param ClickUpConnectionInterface|null $conn ClickUp connection
	 * @param string|null $listId Default list id for create
	 */
	public function __construct(DriverLogger $logger = null, HttpClientInterface $http = null, ClickUpConnectionInterface $conn = null, string $listId = null)
	{
		$this->logger = $logger;
		$this->http = $http;
		$this->conn = $conn;
		$this->listId = $listId;
	}

	/**
	 * Create a task in ClickUp (POST /list/{list_id}/task).
	 *
	 * @param array $data ClickUp payload
	 * @return TrackerResponse Result with new id and raw
	 */
	public function createTask(array $data): TrackerResponse
	{
		if ($this->http && $this->conn && $this->listId) {
			$res = $this->http->post('list/' . $this->listId . '/task', $data, $this->conn->getHeaders());
			if (!empty($res['error']) || ($res['status'] ?? 500) >= 400) {
				return TrackerResponse::error('Failed to create task in ClickUp');
			}
			$body = $res['body'] ?? [];
			return TrackerResponse::success('Task created in ClickUp', ['id' => (string)($body['id'] ?? ''), 'raw' => $body]);
		}
		return TrackerResponse::success('Task created in ClickUp', ['id' => 'CU-1']);
	}

	/**
	 * Update a task in ClickUp (PUT /task/{task_id}).
	 *
	 * @param string $taskId ClickUp task id
	 * @param array $data Payload
	 * @return TrackerResponse Result of update
	 */
	public function updateTask(string $taskId, array $data): TrackerResponse
	{
		if ($this->http && $this->conn) {
			$res = $this->http->put('task/' . $taskId, $data, $this->conn->getHeaders());
			if (!empty($res['error']) || ($res['status'] ?? 500) >= 400) {
				return TrackerResponse::error('Failed to update task in ClickUp');
			}
			return TrackerResponse::success("Task $taskId updated in ClickUp");
		}
		return TrackerResponse::success("Task $taskId updated in ClickUp");
	}

	/**
	 * Delete a ClickUp task (DELETE /task/{task_id}).
	 *
	 * @param string $taskId ClickUp task id
	 * @return TrackerResponse Result of delete
	 */
	public function deleteTask(string $taskId): TrackerResponse
	{
		if ($this->http && $this->conn) {
			$res = $this->http->delete('task/' . $taskId, [], $this->conn->getHeaders());
			if (!empty($res['error']) || ($res['status'] ?? 500) >= 400) {
				return TrackerResponse::error('Failed to delete task from ClickUp');
			}
			return TrackerResponse::success("Task $taskId deleted from ClickUp");
		}
		return TrackerResponse::success("Task $taskId deleted from ClickUp");
	}

	/**
	 * Get a ClickUp task (GET /task/{task_id}).
	 *
	 * @param string $taskId ClickUp task id
	 * @return TrackerResponse Result with normalized data and raw
	 */
	public function getTask(string $taskId): TrackerResponse
	{
		if ($this->http && $this->conn) {
			$res = $this->http->get('task/' . $taskId, [], $this->conn->getHeaders());
			if (!empty($res['error']) || ($res['status'] ?? 500) >= 400) {
				return TrackerResponse::error('Failed to retrieve task from ClickUp');
			}
			$b = $res['body'] ?? [];
			$task = new Task(
				(string)($b['id'] ?? $taskId),
				$b['name'] ?? null,
				$b['text_content'] ?? $b['description'] ?? null,
				$b['assignees'][0]['username'] ?? $b['assignees'][0]['email'] ?? null,
				$b['status']['status'] ?? null,
				$b
			);
			return TrackerResponse::success("Task $taskId retrieved from ClickUp", ['id' => $task->id, 'title' => $task->title, 'raw' => $task->raw]);
		}
		return TrackerResponse::success("Task $taskId retrieved from ClickUp", ['id' => $taskId, 'title' => 'Sample ClickUp task']);
	}
} 