<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Contracts;

interface BitrixConnectionInterface
{
    /**
     * Returns the base URL for Bitrix24 REST requests.
     *
     * @return string
     */
    public function getBaseUrl(): string;

    /**
     * Returns the headers required for requests.
     *
     * @return array
     */
    public function getHeaders(): array;
} 