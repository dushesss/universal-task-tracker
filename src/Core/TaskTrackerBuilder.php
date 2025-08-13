<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Core;

use UniversalTaskTracker\Core\Config\ConfigInterface;
use UniversalTaskTracker\Core\Registry\DriverRegistry;
use UniversalTaskTracker\Logging\DriverLogger;
use UniversalTaskTracker\TrackerManager;

class TaskTrackerBuilder
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var DriverRegistry
     */
    private $registry;

    /**
     * @var DriverLogger|null
     */
    private $logger;

    public function __construct(ConfigInterface $config, DriverRegistry $registry, DriverLogger $logger = null)
    {
        $this->config = $config;
        $this->registry = $registry;
        $this->logger = $logger;
    }

    public function build(): TrackerManager
    {
        $driverName = (string) $this->config->get('trackers.driver', 'bitrix');
        return TrackerManager::create($driverName, $this->logger, $this->registry);
    }
} 