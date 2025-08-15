<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\Asana\Connections;

use UniversalTaskTracker\Contracts\AsanaConnectionInterface;

/**
 * Simple Asana token-based connection (Bearer token).
 */
final class TokenConnection implements AsanaConnectionInterface
{
	/** @var string */
	private $token;
	/** @var string */
	private $baseUrl;

	/**
	 * @param string $token Personal access token
	 * @param string $baseUrl Base URL (default Asana API v1)
	 */
	public function __construct(string $token, string $baseUrl = 'https://app.asana.com/api/1.0/')
	{
		$this->token = $token;
		$this->baseUrl = rtrim($baseUrl, '/') . '/';
	}

	/**
	 * @inheritDoc
	 */
	public function getBaseUrl(): string
	{
		return $this->baseUrl;
	}

	/**
	 * @inheritDoc
	 */
	public function getHeaders(): array
	{
		return [
			'Authorization' => 'Bearer ' . $this->token,
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
		];
	}
} 