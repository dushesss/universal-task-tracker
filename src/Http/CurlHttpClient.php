<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Http;

use UniversalTaskTracker\Http\Contracts\HttpClientInterface;

/**
 * Class CurlHttpClient
 *
 * HTTP client based on native PHP cURL extension. Implements the HttpClientInterface.
 * Supports GET, POST, PUT, DELETE methods. Accepts optional base URL and default headers.
 * Returns decoded JSON body, HTTP status code, response headers, and error message (if any).
 */
class CurlHttpClient implements HttpClientInterface
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $defaultHeaders = [];

    /**
     * CurlHttpClient constructor.
     *
     * @param string $baseUrl Optional base URL for all requests.
     * @param array $defaultHeaders Default headers to include in every request.
     */
    public function __construct(string $baseUrl = '', array $defaultHeaders = [])
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->defaultHeaders = $defaultHeaders;
    }

    /**
     * Send a GET request.
     *
     * @param string $endpoint
     * @param array $query
     * @param array $headers
     * @return array
     */
    public function get(string $endpoint, array $query = [], array $headers = []): array
    {
        return $this->request('GET', $endpoint, $query, null, $headers);
    }

    /**
     * Send a POST request.
     *
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return array
     */
    public function post(string $endpoint, array $data = [], array $headers = []): array
    {
        return $this->request('POST', $endpoint, [], $data, $headers);
    }

    /**
     * Send a PUT request.
     *
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return array
     */
    public function put(string $endpoint, array $data = [], array $headers = []): array
    {
        return $this->request('PUT', $endpoint, [], $data, $headers);
    }

    /**
     * Send a DELETE request.
     *
     * @param string $endpoint
     * @param array $data
     * @param array $headers
     * @return array
     */
    public function delete(string $endpoint, array $data = [], array $headers = []): array
    {
        return $this->request('DELETE', $endpoint, [], $data, $headers);
    }

    /**
     * Internal request handler for all HTTP methods.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $query
     * @param array|null $body
     * @param array $headers
     * @return array Associative array with keys: status, body, headers, error
     */
    protected function request(string $method, string $endpoint, array $query = [], array $body = null, array $headers = []): array
    {
        $url = $this->baseUrl . $endpoint;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $this->prepareHeaders($headers),
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($body !== null) {
            $encoded = json_encode($body);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
                $this->prepareHeaders($headers),
                ['Content-Type: application/json']
            ));
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        if ($error) {
            return [
                'status' => $info['http_code'] ?? 0,
                'body' => null,
                'headers' => [],
                'error' => $error,
            ];
        }

        $headerSize = $info['header_size'];
        $headerRaw = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        return [
            'status' => $info['http_code'],
            'body' => json_decode($body, true),
            'headers' => $this->parseHeaders($headerRaw),
            'error' => null,
        ];
    }

    /**
     * Prepare headers array for cURL.
     *
     * @param array $headers
     * @return array
     */
    protected function prepareHeaders(array $headers): array
    {
        $merged = array_merge($this->defaultHeaders, $headers);
        $result = [];

        foreach ($merged as $key => $value) {
            $result[] = "{$key}: {$value}";
        }

        return $result;
    }

    /**
     * Parse raw HTTP response headers string into associative array.
     *
     * @param string $headerRaw
     * @return array
     */
    protected function parseHeaders(string $headerRaw): array
    {
        $headers = [];
        $lines = explode("\r\n", $headerRaw);

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                $parts = explode(': ', $line, 2);
                $key = $parts[0];
                $value = $parts[1] ?? '';
                $headers[$key] = $value;
            }
        }

        return $headers;
    }
}
