<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\ClickUp\Connections;

use UniversalTaskTracker\Contracts\ClickUpConnectionInterface;

/**
 * ClickUp token connection using API token in Authorization header.
 */
final class TokenConnection implements ClickUpConnectionInterface
{
	/** @var string */
	private $token;
	/** @var string */
	private $baseUrl;

	/**
	 * @param string $token API token
	 * @param string $baseUrl Base URL for ClickUp API v2
	 */
	public function __construct(string $token, string $baseUrl = 'https://api.clickup.com/api/v2/')
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
			'Authorization' => $this->token,
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
		];
	}
} 