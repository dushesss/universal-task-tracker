<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Core\Config;

interface ConfigInterface
{
    /**
     * Get a configuration value by dot-notated key.
     * If key not exists, default will be returned.
     *
     * @param string $key Dot path, e.g. trackers.driver
     * @param mixed $default Default value if key missing
     * @return mixed The found config value or default
     */
    public function get(string $key, $default = null);

    /**
     * Return the whole configuration as array.
     *
     * @return array<string,mixed>
     */
    public function all(): array;
} 