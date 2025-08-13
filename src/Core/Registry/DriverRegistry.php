<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Core\Registry;

use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use InvalidArgumentException;

class DriverRegistry
{
    /**
     * @var array<string, callable>
     */
    private $factories = [];

    /**
     * Register a driver factory by name.
     *
     * @param string $name
     * @param callable():TrackerDriverInterface $factory
     * @return void
     */
    public function register(string $name, callable $factory): void
    {
        $this->factories[strtolower($name)] = $factory;
    }

    /**
     * Create a driver by name.
     *
     * @param string $name
     * @return TrackerDriverInterface
     */
    public function make(string $name): TrackerDriverInterface
    {
        $key = strtolower($name);
        if (!isset($this->factories[$key])) {
            throw new InvalidArgumentException("Unknown driver: {$name}");
        }
        $factory = $this->factories[$key];
        return $factory();
    }
} 