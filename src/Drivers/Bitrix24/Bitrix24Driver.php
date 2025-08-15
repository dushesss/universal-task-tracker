<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\Bitrix24;

use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use UniversalTaskTracker\DTO\TrackerResponse;
use UniversalTaskTracker\Logging\DriverLogger;
use UniversalTaskTracker\Http\Contracts\HttpClientInterface;
use UniversalTaskTracker\Contracts\BitrixConnectionInterface;
use UniversalTaskTracker\DTO\Task;

/**
 * Bitrix24 driver implementing TrackerDriverInterface.
 *
 * Provides real REST calls when HttpClientInterface and BitrixConnectionInterface are supplied,
 * and falls back to mocked behavior otherwise.
 */
class Bitrix24Driver implements TrackerDriverInterface
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
     * Optional Bitrix24 connection (base URL and headers provider).
     * @var BitrixConnectionInterface|null
     */
    protected $connection;

    /**
     * Create Bitrix24 driver instance.
     *
     * @param DriverLogger|null $logger Optional logger for tracking driver actions.
     * @param HttpClientInterface|null $httpClient HTTP client for real API calls.
     * @param BitrixConnectionInterface|null $connection Connection details provider.
     */
    public function __construct(DriverLogger $logger = null, HttpClientInterface $httpClient = null, BitrixConnectionInterface $connection = null)
    {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->connection = $connection;
    }


    /**
     * Create a new task in Bitrix24.
     *
     * @param array $data Task data to be sent to Bitrix24.
     * @return TrackerResponse Result with new id and raw body if available
     */
    public function createTask(array $data): TrackerResponse
    {
        if ($this->httpClient && $this->connection) {
            try {
                $headers = $this->connection->getHeaders();
                // Bitrix24 task creation usually via POST /rest/task.add
                $response = $this->httpClient->post('/rest/task.add', $data, $headers);
                if (!empty($response['error']) || ($response['status'] ?? 500) >= 400) {
                    return TrackerResponse::error('Failed to create task in Bitrix24');
                }
                $body = $response['body'] ?? [];
                $taskId = $body['result']['task']['id'] ?? $body['result'] ?? null;
                return TrackerResponse::success('Task created in Bitrix24', ['id' => (string) $taskId, 'raw' => $body]);
            } catch (\Throwable $e) {
                return TrackerResponse::error('Exception while creating task in Bitrix24');
            }
        }
        return TrackerResponse::success('Task created in Bitrix24', ['id' => '123']);
    }

    /**
     * Update an existing task in Bitrix24.
     *
     * @param string $taskId ID of the Bitrix24 task to update.
     * @param array $data Updated data for the task.
     * @return TrackerResponse Result of update
     */
    public function updateTask(string $taskId, array $data): TrackerResponse
    {
        if ($this->httpClient && $this->connection) {
            try {
                $headers = $this->connection->getHeaders();
                $payload = array_merge(['taskId' => $taskId], $data);
                $response = $this->httpClient->post('/rest/task.update', $payload, $headers);
                if (!empty($response['error']) || ($response['status'] ?? 500) >= 400) {
                    return TrackerResponse::error('Failed to update task in Bitrix24');
                }
                return TrackerResponse::success("Task $taskId updated in Bitrix24");
            } catch (\Throwable $e) {
                return TrackerResponse::error('Exception while updating task in Bitrix24');
            }
        }
        return TrackerResponse::success("Task $taskId updated in Bitrix24");
    }

    /**
     * Delete a task in Bitrix24.
     *
     * @param string $taskId ID of the Bitrix24 task to delete.
     * @return TrackerResponse Result of delete
     */
    public function deleteTask(string $taskId): TrackerResponse
    {
        if ($this->httpClient && $this->connection) {
            try {
                $headers = $this->connection->getHeaders();
                $response = $this->httpClient->post('/rest/task.delete', ['taskId' => $taskId], $headers);
                if (!empty($response['error']) || ($response['status'] ?? 500) >= 400) {
                    return TrackerResponse::error('Failed to delete task from Bitrix24');
                }
                return TrackerResponse::success("Task $taskId deleted from Bitrix24");
            } catch (\Throwable $e) {
                return TrackerResponse::error('Exception while deleting task from Bitrix24');
            }
        }
        return TrackerResponse::success("Task $taskId deleted from Bitrix24");
    }

    /**
     * Retrieve a task from Bitrix24 by ID.
     *
     * @param string $taskId ID of the Bitrix24 task to retrieve.
     * @return TrackerResponse Result with normalized data and raw body
     */
    public function getTask(string $taskId): TrackerResponse
    {
        if ($this->httpClient && $this->connection) {
            try {
                $headers = $this->connection->getHeaders();
                // Example endpoint; Bitrix24 tasks API uses RPC-like endpoints
                $response = $this->httpClient->get('/rest/task.get', ['taskId' => $taskId], $headers);

                if (!empty($response['error']) || ($response['status'] ?? 500) >= 400) {
                    return TrackerResponse::error('Failed to retrieve task from Bitrix24');
                }

                $body = $response['body'] ?? null;
                $task = new Task(
                    $taskId,
                    $body['result']['title'] ?? null,
                    $body['result']['description'] ?? null,
                    $body['result']['responsible'] ?? null,
                    $body['result']['status'] ?? null,
                    $body
                );
                return TrackerResponse::success("Task $taskId retrieved from Bitrix24", [
                    'id' => $task->id,
                    'title' => $task->title,
                    'raw' => $task->raw,
                ]);
            } catch (\Throwable $e) {
                return TrackerResponse::error('Exception while retrieving task from Bitrix24');
            }
        }

        return TrackerResponse::success("Task $taskId retrieved from Bitrix24", [
            'id' => $taskId,
            'title' => 'Sample task',
        ]);
    }
}
