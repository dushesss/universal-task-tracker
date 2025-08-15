<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\Asana;

use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use UniversalTaskTracker\DTO\TrackerResponse;
use UniversalTaskTracker\DTO\Task;
use UniversalTaskTracker\Http\Contracts\HttpClientInterface;
use UniversalTaskTracker\Logging\DriverLogger;
use UniversalTaskTracker\Contracts\AsanaConnectionInterface;

final class AsanaDriver implements TrackerDriverInterface
{
	private $logger;
	private $http;
	private $conn;
	private $workspaceGid;
	private $projectGid; // optional

	/**
	 * Create Asana driver instance.
	 *
	 * @param DriverLogger|null $logger Optional logger
	 * @param HttpClientInterface|null $http PSR-based HTTP client
	 * @param AsanaConnectionInterface|null $conn Asana connection
	 * @param string|null $workspaceGid Default workspace gid
	 * @param string|null $projectGid Default project gid (optional)
	 */
	public function __construct(DriverLogger $logger = null, HttpClientInterface $http = null, AsanaConnectionInterface $conn = null, string $workspaceGid = null, string $projectGid = null)
	{
		$this->logger = $logger;
		$this->http = $http;
		$this->conn = $conn;
		$this->workspaceGid = $workspaceGid;
		$this->projectGid = $projectGid;
	}

	/**
	 * Create a task in Asana (POST /tasks).
	 *
	 * @param array $data Asana payload under `data`
	 * @return TrackerResponse Result with new gid and raw
	 */
	public function createTask(array $data): TrackerResponse
	{
		if ($this->http && $this->conn) {
			$payload = ['data' => $data];
			if ($this->workspaceGid && empty($payload['data']['workspace'])) {
				$payload['data']['workspace'] = $this->workspaceGid;
			}
			if ($this->projectGid && empty($payload['data']['projects'])) {
				$payload['data']['projects'] = [$this->projectGid];
			}
			$res = $this->http->post('tasks', $payload, $this->conn->getHeaders());
			if (!empty($res['error']) || ($res['status'] ?? 500) >= 400) {
				return TrackerResponse::error('Failed to create task in Asana');
			}
			$body = $res['body']['data'] ?? [];
			return TrackerResponse::success('Task created in Asana', ['id' => (string)($body['gid'] ?? ''), 'raw' => $res['body'] ?? null]);
		}
		return TrackerResponse::success('Task created in Asana', ['id' => 'ASANA-1']);
	}

	/**
	 * Update an Asana task (PUT /tasks/{gid}).
	 *
	 * @param string $taskId Task gid
	 * @param array $data Payload under `data`
	 * @return TrackerResponse Result of update
	 */
	public function updateTask(string $taskId, array $data): TrackerResponse
	{
		if ($this->http && $this->conn) {
			$res = $this->http->put('tasks/' . $taskId, ['data' => $data], $this->conn->getHeaders());
			if (!empty($res['error']) || ($res['status'] ?? 500) >= 400) {
				return TrackerResponse::error('Failed to update task in Asana');
			}
			return TrackerResponse::success("Task $taskId updated in Asana");
		}
		return TrackerResponse::success("Task $taskId updated in Asana");
	}

	/**
	 * Delete an Asana task (DELETE /tasks/{gid}).
	 *
	 * @param string $taskId Task gid
	 * @return TrackerResponse Result of delete
	 */
	public function deleteTask(string $taskId): TrackerResponse
	{
		if ($this->http && $this->conn) {
			$res = $this->http->delete('tasks/' . $taskId, [], $this->conn->getHeaders());
			if (!empty($res['error']) || ($res['status'] ?? 500) >= 400) {
				return TrackerResponse::error('Failed to delete task from Asana');
			}
			return TrackerResponse::success("Task $taskId deleted from Asana");
		}
		return TrackerResponse::success("Task $taskId deleted from Asana");
	}

	/**
	 * Get an Asana task by gid (GET /tasks/{gid}).
	 *
	 * @param string $taskId Task gid
	 * @return TrackerResponse Result with normalized data and raw
	 */
	public function getTask(string $taskId): TrackerResponse
	{
		if ($this->http && $this->conn) {
			$res = $this->http->get('tasks/' . $taskId, [], $this->conn->getHeaders());
			if (!empty($res['error']) || ($res['status'] ?? 500) >= 400) {
				return TrackerResponse::error('Failed to retrieve task from Asana');
			}
			$body = $res['body']['data'] ?? [];
			$task = new Task(
				(string)($body['gid'] ?? $taskId),
				$body['name'] ?? null,
				$body['notes'] ?? null,
				$body['assignee']['name'] ?? null,
				isset($body['completed']) ? ($body['completed'] ? 'Completed' : 'Open') : null,
				$body
			);
			return TrackerResponse::success("Task $taskId retrieved from Asana", ['id' => $task->id, 'title' => $task->title, 'raw' => $task->raw]);
		}
		return TrackerResponse::success("Task $taskId retrieved from Asana", ['id' => $taskId, 'title' => 'Sample Asana task']);
	}
} 