<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Core\Config;

interface ConfigInterface
{
    /**
     * Get a configuration value by dot-notated key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Return the whole configuration as array.
     *
     * @return array
     */
    public function all(): array;
} 