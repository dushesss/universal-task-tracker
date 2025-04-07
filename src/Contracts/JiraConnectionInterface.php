<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Contracts;

/**
 * Interface JiraConnectionInterface
 *
 * Describes how a Jira connection must provide base URL and headers.
 */
interface JiraConnectionInterface
{
    /**
     * Returns the base URL for Jira API requests.
     *
     * @return string
     */
    public function getBaseUrl(): string;

    /**
     * Returns the headers required for authenticated requests.
     *
     * @return array
     */
    public function getHeaders(): array;
}
