<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Core\Registry;

use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use InvalidArgumentException;

/**
 * DriverRegistry
 *
 * Keeps mapping from driver name to factory callables.
 */
class DriverRegistry
{
    /**
     * @var array<string, callable():TrackerDriverInterface>
     */
    private $factories = [];

    /**
     * Register a driver factory by name.
     *
     * @param string $name Driver name (case-insensitive)
     * @param callable():TrackerDriverInterface $factory Factory that creates driver instance
     * @return void
     */
    public function register(string $name, callable $factory): void
    {
        $this->factories[strtolower($name)] = $factory;
    }

    /**
     * Create a driver by name.
     *
     * @param string $name Driver name
     * @return TrackerDriverInterface Concrete driver
     * @throws InvalidArgumentException When driver not found
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