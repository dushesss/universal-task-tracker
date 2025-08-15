<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Contracts;

/**
 * AsanaConnectionInterface
 *
 * Provides base URL and headers for Asana API requests.
 */
interface AsanaConnectionInterface
{
	/**
	 * Return base URL for Asana API (e.g., https://app.asana.com/api/1.0/).
	 *
	 * @return string Base URL
	 */
	public function getBaseUrl(): string;

	/**
	 * Return HTTP headers including Authorization.
	 *
	 * @return array<string,string> Headers map
	 */
	public function getHeaders(): array;
} 