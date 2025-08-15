<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Contracts;

/**
 * ClickUpConnectionInterface
 *
 * Provides base URL and headers for ClickUp API requests.
 */
interface ClickUpConnectionInterface
{
	/**
	 * @return string Base URL for ClickUp API (e.g., https://api.clickup.com/api/v2/)
	 */
	public function getBaseUrl(): string;

	/**
	 * @return array<string,string> Headers map including Authorization token
	 */
	public function getHeaders(): array;
} 