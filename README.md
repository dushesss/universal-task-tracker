
#  Universal Task Tracker (Draft Version)

> This is a **draft version** of the documentation. The package is in active development and may change significantly.

A universal PHP package to work with external task trackers like **Bitrix24** and **Jira**, using a unified API.  
Supports multiple connection types, flexible HTTP transport (cURL or custom), and works both inside **Laravel** and standalone PHP applications.

---

## Features

- ✅ Unified driver interface: `createTask`, `updateTask`, `deleteTask`, `getTask`
- ✅ Ready-to-use drivers: **Bitrix24**, **Jira (Cloud & Server)**
- ✅ Works with **PHP 7.0+**
- ✅ Optional logging to file
- ✅ Custom HTTP transport (`HttpClientInterface`)
- ✅ Laravel support: auto-binding, config publishing

---

## Installation

```bash
composer require your-vendor/universal-task-tracker
```

---

## Quick Example (Vanilla PHP)

```php
use UniversalTaskTracker\Facades\TaskTracker;
use UniversalTaskTracker\Http\CurlHttpClient;

// Optional: initialize HTTP client manually
$http = new CurlHttpClient('https://your-bitrix24-url.com/', [
    'Authorization' => 'Bearer YOUR_TOKEN',
]);

// Use tracker
TaskTracker::use('bitrix'); // Or provide $logger and $dryRun if needed

$response = TaskTracker::createTask([
    'title' => 'My Test Task',
]);

if ($response->success) {
    echo "Task created: " . json_encode($response->data);
}
```

---

## Laravel Integration

### 1. Publish config

```bash
php artisan vendor:publish --tag=task-tracker-config
```

### 2. Update `.env`

```dotenv
TRACKER_DRIVER=jira

JIRA_CONNECTION=cloud_token
JIRA_EMAIL=your-email@example.com
JIRA_API_TOKEN=your_api_token
JIRA_DOMAIN=your-domain.atlassian.net
```

### 3. Use facade

```php
use UniversalTaskTracker\Facades\TaskTracker;

TaskTracker::createTask([
    'title' => 'Hello from Laravel',
]);
```

---

## Configuration Options

`config/trackers.php`

```php
return [
    'driver' => env('TRACKER_DRIVER', 'bitrix'),

    'log_path' => env('TRACKER_LOG_PATH', storage_path('logs/task-tracker.log')),

    'jira' => [
        'connection' => env('JIRA_CONNECTION', 'cloud_token'),

        'cloud_token' => [
            'email' => env('JIRA_EMAIL'),
            'api_token' => env('JIRA_API_TOKEN'),
            'domain' => env('JIRA_DOMAIN'),
        ],

        'server_basic' => [
            'username' => env('JIRA_USER'),
            'password' => env('JIRA_PASS'),
            'base_url' => env('JIRA_BASE_URL'),
        ],
    ],
];
```

---

##  Supported Drivers

| Driver    | Connection Types      | Auth                   | Status     |
|-----------|------------------------|------------------------|------------|
| `bitrix`  | REST                   | Access token           | ✅ Ready    |
| `jira`    | cloud_token, server_basic | Basic Auth, API token | ✅ Ready    |

---

##  Response Structure

All operations return a `TrackerResponse` object:

```php
class TrackerResponse {
    public bool $success;
    public string $message;
    public array|null $data;
}
```

---

##  Custom HTTP Clients

The package uses `HttpClientInterface`. You can implement your own (e.g., with Guzzle):

```php
interface HttpClientInterface {
    public function get(string $url, array $query = [], array $headers = []);
    public function post(string $url, array $data = [], array $headers = []);
    public function put(string $url, array $data = [], array $headers = []);
    public function delete(string $url, array $data = [], array $headers = []);
}
```

---

## Testing

```bash
vendor/bin/phpunit
```

---

## Roadmap

- [x] Bitrix24 driver
- [x] Jira driver (Cloud / Server)
- [x] Logging support
- [ ] Async queue jobs
- [ ] Webhook support
- [ ] Optional Guzzle adapter
- [ ] Driver auto-discovery

---
