<?php

declare(strict_types=1);

namespace UniversalTaskTracker\Drivers\Jira\Connections;

use UniversalTaskTracker\Contracts\JiraConnectionInterface;

/**
 * Jira Cloud connection using email + API token (Basic Auth).
 */
class CloudTokenConnection implements JiraConnectionInterface
{
    /** @var string */
    protected $email;
    /** @var string */
    protected $token;
    /** @var string */
    protected $domain;

    /**
     * @param array{email:string,api_token:string,domain:string} $config Config with email, api_token and domain
     */
    public function __construct(array $config)
    {
        $this->email = $config['email'];
        $this->token = $config['api_token'];
        $this->domain = $config['domain'];
    }

    /**
     * Get base URL for Jira Cloud REST API.
     *
     * @return string Base URL ending with /rest/api/3/
     */
    public function getBaseUrl(): string
    {
        return "https://{$this->domain}/rest/api/3/";
    }

    /**
     * Get headers including Basic Authorization.
     *
     * @return array<string,string> Headers map
     */
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
