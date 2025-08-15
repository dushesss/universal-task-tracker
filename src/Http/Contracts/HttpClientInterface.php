<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Http\Contracts;

/**
 * HttpClientInterface
 *
 * A small interface for making HTTP requests. Methods usually
 * returns an array with keys: status, body, headers, error.
 */
interface HttpClientInterface
{
	/**
	 * Send a GET request.
	 * It may return decoded JSON body when server respond with JSON.
	 *
	 * @param string $endpoint Relative or absolute path
	 * @param array $query Query params as associative array
	 * @param array $headers Extra headers
	 * @return array{status:int,body:mixed,headers:array,error:?string}
	 */
	public function get(string $endpoint, array $query = [], array $headers = []): array;

	/**
	 * Send a POST request.
	 *
	 * @param string $endpoint Relative or absolute path
	 * @param array $data Payload that will be JSON-encoded by client
	 * @param array $headers Extra headers
	 * @return array{status:int,body:mixed,headers:array,error:?string}
	 */
	public function post(string $endpoint, array $data = [], array $headers = []): array;

	/**
	 * Send a PUT request.
	 *
	 * @param string $endpoint Relative or absolute path
	 * @param array $data Payload that will be JSON-encoded by client
	 * @param array $headers Extra headers
	 * @return array{status:int,body:mixed,headers:array,error:?string}
	 */
	public function put(string $endpoint, array $data = [], array $headers = []): array;

	/**
	 * Send a DELETE request.
	 *
	 * @param string $endpoint Relative or absolute path
	 * @param array $data Optional payload
	 * @param array $headers Extra headers
	 * @return array{status:int,body:mixed,headers:array,error:?string}
	 */
	public function delete(string $endpoint, array $data = [], array $headers = []): array;
}
