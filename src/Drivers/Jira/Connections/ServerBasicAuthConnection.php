<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\Jira\Connections;

use UniversalTaskTracker\Contracts\JiraConnectionInterface;

/**
 * Jira Server (on-premise) connection using login + password (Basic Auth).
 */
class ServerBasicAuthConnection implements JiraConnectionInterface
{
    /** @var string */
    protected $username;
    /** @var string */
    protected $password;
    /** @var string */
    protected $baseUrl;

    /**
     * @param array{username:string,password:string,base_url:string} $config Server credentials and base URL
     */
    public function __construct(array $config)
    {
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->baseUrl = rtrim($config['base_url'], '/') . '/rest/api/2/';
    }

    /**
     * @return string Base URL with /rest/api/2/
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return array<string,string> Headers map with Basic Authorization
     */
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