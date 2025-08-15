<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Http;

use Psr\Http\Client\ClientInterface as Psr18Client;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use UniversalTaskTracker\Http\Contracts\HttpClientInterface;

/**
 * PSR-18/PSR-7/PSR-17 adapter that implements the package's HttpClientInterface.
 *
 * This adapter allows using any PSR-18 HTTP client (e.g., Guzzle, Symfony HttpClient)
 * and any PSR-17 factories for building requests and streams.
 */
class Psr18HttpClient implements HttpClientInterface
{
	/** @var Psr18Client */
	private $client;
	/** @var RequestFactoryInterface */
	private $requestFactory;
	/** @var StreamFactoryInterface */
	private $streamFactory;
	/** @var string */
	private $baseUrl;
	/** @var array */
	private $defaultHeaders;

	/**
	 * Create adapter instance.
	 *
	 * @param Psr18Client $client PSR-18 client
	 * @param RequestFactoryInterface $requestFactory PSR-17 request factory
	 * @param StreamFactoryInterface $streamFactory PSR-17 stream factory
	 * @param string $baseUrl Optional base URL, like https://api.example.com/
	 * @param array $defaultHeaders Default headers applied to each request
	 */
	public function __construct(Psr18Client $client, RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory, string $baseUrl = '', array $defaultHeaders = [])
	{
		$this->client = $client;
		$this->requestFactory = $requestFactory;
		$this->streamFactory = $streamFactory;
		$this->baseUrl = rtrim($baseUrl, '/');
		$this->defaultHeaders = $defaultHeaders;
	}

	/**
	 * Send a GET request.
	 *
	 * @param string $endpoint Relative path
	 * @param array $query Query params
	 * @param array $headers Extra headers
	 * @return array{status:int,body:mixed,headers:array,error:?string}
	 */
	public function get(string $endpoint, array $query = [], array $headers = []): array
	{
		return $this->send('GET', $endpoint, $query, null, $headers);
	}

	/**
	 * Send a POST request.
	 *
	 * @param string $endpoint Relative path
	 * @param array $data JSON payload
	 * @param array $headers Extra headers
	 * @return array{status:int,body:mixed,headers:array,error:?string}
	 */
	public function post(string $endpoint, array $data = [], array $headers = []): array
	{
		return $this->send('POST', $endpoint, [], $data, $headers);
	}

	/**
	 * Send a PUT request.
	 *
	 * @param string $endpoint Relative path
	 * @param array $data JSON payload
	 * @param array $headers Extra headers
	 * @return array{status:int,body:mixed,headers:array,error:?string}
	 */
	public function put(string $endpoint, array $data = [], array $headers = []): array
	{
		return $this->send('PUT', $endpoint, [], $data, $headers);
	}

	/**
	 * Send a DELETE request.
	 *
	 * @param string $endpoint Relative path
	 * @param array $data Optional JSON payload
	 * @param array $headers Extra headers
	 * @return array{status:int,body:mixed,headers:array,error:?string}
	 */
	public function delete(string $endpoint, array $data = [], array $headers = []): array
	{
		return $this->send('DELETE', $endpoint, [], $data, $headers);
	}

	/**
	 * Send a request using PSR-18 client.
	 * Returns normalized array similar to CurlHttpClient.
	 *
	 * @param string $method HTTP method (GET/POST/PUT/DELETE)
	 * @param string $endpoint Relative path
	 * @param array $query Query params
	 * @param array|null $body JSON body or null
	 * @param array $headers Extra headers
	 * @return array{status:int,body:mixed,headers:array,error:?string}
	 */
	private function send(string $method, string $endpoint, array $query = [], array $body = null, array $headers = []): array
	{
		$url = $this->baseUrl . $endpoint;
		if (!empty($query)) {
			$url .= '?' . http_build_query($query);
		}
		$request = $this->requestFactory->createRequest($method, $url);

		$merged = array_merge($this->defaultHeaders, $headers);
		foreach ($merged as $key => $value) {
			$request = $request->withHeader($key, $value);
		}

		if ($body !== null) {
			$json = json_encode($body);
			$stream = $this->streamFactory->createStream($json);
			$request = $request->withBody($stream)
				->withHeader('Content-Type', 'application/json');
		}

		$response = $this->client->sendRequest($request);

		$respHeaders = [];
		foreach ($response->getHeaders() as $name => $values) {
			$respHeaders[$name] = implode(', ', $values);
		}

		$respBody = (string) $response->getBody();
		return [
			'status' => $response->getStatusCode(),
			'body' => $respBody !== '' ? json_decode($respBody, true) : null,
			'headers' => $respHeaders,
			'error' => null,
		];
	}
} 