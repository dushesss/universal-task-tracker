<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Http\Contracts;

/**
 * Interface HttpClientInterface
 *
 * Defines a universal interface for making HTTP requests.
 *
 * TODO need to implement other realizations for HTTP requests, like Guzzle or Symfony variants
 */
interface HttpClientInterface
{
    /**
     * Send a GET request.
     *
     * @param string $endpoint
     * @param array $query
     * @param array $headers
     * @return array
     */
    public function get(string $endpoint, array $query = [], array $headers = []): array;

    /**
     * Send a POST request.
     *
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return array
     */
    public function post(string $endpoint, array $data = [], array $headers = []): array;

    /**
     * Send a PUT request.
     *
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return array
     */
    public function put(string $endpoint, array $data = [], array $headers = []): array;

    /**
     * Send a DELETE request.
     *
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return array
     */
    public function delete(string $endpoint, array $data = [], array $headers = []): array;
}
