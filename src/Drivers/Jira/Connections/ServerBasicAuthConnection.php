<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\Jira\Connections;

use UniversalTaskTracker\Contracts\JiraConnectionInterface;


/**
 * Class ServerBasicAuthConnection
 *
 * Jira Server (on-premise) connection using login + password.
 */
class ServerBasicAuthConnection implements JiraConnectionInterface
{
    protected $username;
    protected $password;
    protected $baseUrl;

    public function __construct(array $config)
    {
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->baseUrl = rtrim($config['base_url'], '/') . '/rest/api/2/';
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getHeaders(): array
    {
        $auth = base64_encode("{$this->username}:{$this->password}");

        return [
            'Authorization' => "Basic {$auth}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}