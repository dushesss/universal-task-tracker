<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\Jira\Connections;

use UniversalTaskTracker\Contracts\JiraConnectionInterface;
use InvalidArgumentException;

/**
 * Factory for creating Jira connection instances from array config.
 */
class JiraConnectionFactory
{
    /**
     * Create a Jira connection instance based on configuration.
     *
     * @param array $config Config with 'connection' key and nested settings
     * @return JiraConnectionInterface Concrete connection
     * @throws InvalidArgumentException When type is missing or unsupported
     */
    public static function make(array $config): JiraConnectionInterface
    {
        if (!isset($config['connection'])) {
            throw new InvalidArgumentException("Missing 'connection' type in Jira config.");
        }

        switch ($config['connection']) {
            case 'cloud_token':
                return new CloudTokenConnection($config['cloud_token']);

            case 'server_basic':
                return new ServerBasicAuthConnection($config['server_basic']);

            default:
                throw new InvalidArgumentException("Unsupported Jira connection type: {$config['connection']}");
        }
    }
}
