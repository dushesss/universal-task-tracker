<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\Jira\Connections;

use UniversalTaskTracker\Contracts\JiraConnectionInterface;

/**
 * Class CloudTokenConnection
 *
 * Jira Cloud connection using email + API token (Basic Auth).
 */
class CloudTokenConnection implements JiraConnectionInterface
{
    protected $email;
    protected $token;
    protected $domain;

    public function __construct(array $config)
    {
        $this->email = $config['email'];
        $this->token = $config['api_token'];
        $this->domain = $config['domain'];
    }

    public function getBaseUrl(): string
    {
        return "https://{$this->domain}/rest/api/3/";
    }

    public function getHeaders(): array
    {
        $auth = base64_encode("{$this->email}:{$this->token}");

        return [
            'Authorization' => "Basic {$auth}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}
