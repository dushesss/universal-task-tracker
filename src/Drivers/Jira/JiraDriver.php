<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\Jira;

use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use UniversalTaskTracker\DTO\TrackerResponse;
use UniversalTaskTracker\Logging\DriverLogger;
use UniversalTaskTracker\Http\Contracts\HttpClientInterface;
use UniversalTaskTracker\Contracts\JiraConnectionInterface;
use UniversalTaskTracker\DTO\Task;

/**
 * Jira driver implementing TrackerDriverInterface.
 *
 * Provides real REST calls when HttpClientInterface and JiraConnectionInterface are supplied,
 * and falls back to mocked behavior otherwise.
 */
class JiraDriver implements TrackerDriverInterface
{
	/**
	 * Optional logger for driver actions.
	 * @var DriverLogger|null
	 */
	protected $logger;

	/**
	 * Optional PSR-compatible HTTP client adapter.
	 * @var HttpClientInterface|null
	 */
	protected $httpClient;

	/**
	 * Optional Jira connection (base URL and headers provider).
	 * @var JiraConnectionInterface|null
	 */
	protected $connection;

	public function __construct(DriverLogger $logger = null, HttpClientInterface $httpClient = null, JiraConnectionInterface $connection = null)
	{
		$this->logger = $logger;
		$this->httpClient = $httpClient;
		$this->connection = $connection;
	}

	/**
	 * Create a new Jira issue.
	 */
	public function createTask(array $data): TrackerResponse
	{
		if ($this->httpClient && $this->connection) {
			try {
				$headers = $this->connection->getHeaders();
				$response = $this->httpClient->post('issue', $data, $headers);

				if (!empty($response['error']) || ($response['status'] ?? 500) >= 400) {
					return TrackerResponse::error('Failed to create task in Jira');
				}

				$body = $response['body'] ?? [];
				$taskId = $body['key'] ?? $body['id'] ?? null;
				return TrackerResponse::success('Task created in Jira', ['id' => (string) $taskId, 'raw' => $body]);
			} catch (\Throwable $e) {
				return TrackerResponse::error('Exception while creating task in Jira');
			}
		}

		return TrackerResponse::success('Task created in Jira', ['id' => 'JIRA-456']);
	}

	/**
	 * Update an existing Jira issue.
	 */
	public function updateTask(string $taskId, array $data): TrackerResponse
	{
		if ($this->httpClient && $this->connection) {
			try {
				$headers = $this->connection->getHeaders();
				$response = $this->httpClient->put('issue/' . $taskId, $data, $headers);

				if (!empty($response['error']) || ($response['status'] ?? 500) >= 400) {
					return TrackerResponse::error('Failed to update task in Jira');
				}

				return TrackerResponse::success("Task $taskId updated in Jira");
			} catch (\Throwable $e) {
				return TrackerResponse::error('Exception while updating task in Jira');
			}
		}

		return TrackerResponse::success("Task $taskId updated in Jira");
	}

	/**
	 * Delete a Jira issue.
	 */
	public function deleteTask(string $taskId): TrackerResponse
	{
		if ($this->httpClient && $this->connection) {
			try {
				$headers = $this->connection->getHeaders();
				$response = $this->httpClient->delete('issue/' . $taskId, [], $headers);

				if (!empty($response['error']) || ($response['status'] ?? 500) >= 400) {
					return TrackerResponse::error('Failed to delete task from Jira');
				}

				return TrackerResponse::success("Task $taskId deleted from Jira");
			} catch (\Throwable $e) {
				return TrackerResponse::error('Exception while deleting task from Jira');
			}
		}

		return TrackerResponse::success("Task $taskId deleted from Jira");
	}

	/**
	 * Retrieve a Jira issue by ID or key.
	 */
	public function getTask(string $taskId): TrackerResponse
	{
		if ($this->httpClient && $this->connection) {
			try {
				$headers = $this->connection->getHeaders();
				$response = $this->httpClient->get('issue/' . $taskId, [], $headers);

				if (!empty($response['error']) || ($response['status'] ?? 500) >= 400) {
					return TrackerResponse::error('Failed to retrieve task from Jira');
				}

				$body = $response['body'] ?? null;
				$task = new Task(
					$taskId,
					$body['fields']['summary'] ?? null,
					$body['fields']['description'] ?? null,
					$body['fields']['assignee']['displayName'] ?? null,
					$body['fields']['status']['name'] ?? null,
					$body
				);
				return TrackerResponse::success("Task $taskId retrieved from Jira", [
					'id' => $task->id,
					'title' => $task->title,
					'raw' => $task->raw,
				]);
			} catch (\Throwable $e) {
				return TrackerResponse::error('Exception while retrieving task from Jira');
			}
		}

		return TrackerResponse::success("Task $taskId retrieved from Jira", [
			'id' => $taskId,
			'title' => 'Sample Jira task',
		]);
	}
}
