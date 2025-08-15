<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Contracts;

/**
 * JiraConnectionInterface
 *
 * Provides base URL and headers for Jira API requests.
 */
interface JiraConnectionInterface
{
    /**
     * Returns the base URL for Jira API requests.
     *
     * @return string Base URL ending with /rest/api/{version}/
     */
    public function getBaseUrl(): string;

    /**
     * Returns headers required for authenticated requests.
     *
     * @return array<string,string> Headers map
     */
    public function getHeaders(): array;
}
