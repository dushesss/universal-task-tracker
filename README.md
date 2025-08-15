
# Universal Task Tracker

English | [Русский](#русский)

A framework-agnostic PHP package that provides a unified facade over multiple task tracker APIs (e.g., Jira, Bitrix24).

- Unified driver interface: createTask, updateTask, deleteTask, getTask
- Drivers: Jira (Cloud/Server), Bitrix24
- Pluggable HTTP client (PSR-18 adapter provided), custom or cURL
- Optional file logging
- Laravel bridge included (config publish, provider)

## Contents
- [Installation](#installation)
- [Quick Start (Plain PHP)](#quick-start-plain-php)
- [Laravel](#laravel)
- [Configuration (example)](#configuration-example)
- [HTTP Client](#http-client)
- [Response](#response)
- [Writing custom drivers](#writing-custom-drivers)
- [Use cases — why this package](#use-cases--why-this-package)
- [Examples](#examples)
- [Русский](#русский)

## Installation

```bash
composer require dushesss/universal-task-tracker
```

[Back to top](#contents)

## Quick Start (Plain PHP)

```php
use UniversalTaskTracker\Facades\TaskTracker;

TaskTracker::use('bitrix');

$response = TaskTracker::createTask(['title' => 'My Task']);
if ($response->success) {
    echo $response->data['id'];
}
```

For real API calls, provide a PSR-18 client and a connection object to the driver (see examples in drivers and Http/Psr18HttpClient).

[Back to top](#contents)

## Laravel

- The service provider is auto-discovered: `UniversalTaskTracker\Bridge\Laravel\ServiceProvider`.
- Publish config:

```bash
php artisan vendor:publish --tag=task-tracker-config
```

Set `TRACKER_DRIVER` in your `.env`.

[Back to top](#contents)

## Configuration (example)

```php
return [
    'driver' => getenv('TRACKER_DRIVER') ?: 'bitrix',
    'log_path' => getenv('TRACKER_LOG_PATH') ?: __DIR__ . '/../storage/logs/task-tracker.log',
];
```

[Back to top](#contents)

## HTTP Client

- Built-in: `CurlHttpClient`
- PSR-18 adapter: `Psr18HttpClient` (use with Guzzle/Symfony HttpClient and PSR-17 factories)

[Back to top](#contents)

## Response

```php
class TrackerResponse {
    public bool $success;
    public string $message;
    public ?array $data;
}
```

[Back to top](#contents)

## Writing custom drivers

You can add support for your own tracker/CRM without changing this package:

1) Implement the driver
```php
use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use UniversalTaskTracker\DTO\TrackerResponse;

final class CustomCrmDriver implements TrackerDriverInterface {
    public function createTask(array $data): TrackerResponse { /* ... */ }
    public function updateTask(string $id, array $data): TrackerResponse { /* ... */ }
    public function deleteTask(string $id): TrackerResponse { /* ... */ }
    public function getTask(string $id): TrackerResponse { /* ... */ }
}
```

2) (Optional) Create a connection class for auth/headers
```php
final class CustomCrmConnection {
    public function __construct(private string $baseUrl, private string $token) {}
    public function getBaseUrl(): string { return rtrim($this->baseUrl, '/') . '/api/'; }
    public function getHeaders(): array { return ['Authorization' => "Bearer {$this->token}"]; }
}
```

3) Register driver in your app via `DriverRegistry`
```php
use UniversalTaskTracker\Core\Registry\DriverRegistry;
$registry = new DriverRegistry();
$registry->register('customcrm', fn() => new CustomCrmDriver(/* http, conn, logger */));
```

4) Build manager with your registry
```php
use UniversalTaskTracker\Core\TaskTrackerBuilder;
use UniversalTaskTracker\Core\Config\ArrayConfig;

$config  = new ArrayConfig(['trackers' => ['driver' => 'customcrm']]);
$builder = new TaskTrackerBuilder($config, $registry);
$manager = $builder->build();
```

In Laravel you can do the same in your own service provider (from your app), then call `TaskTracker::setManager($manager)`.

[Back to top](#contents)

## Use cases — why this package

- One unified API instead of many SDKs (Jira, Bitrix24, ClickUp, Asana, etc.)
- Easy plug-in of a custom driver for in-house CRM
- Web “Report a bug” form: creates a ticket with screenshots/URL/user agent
- “Suggest a feature” form: routes to the right project with labels and priority
- CI/CD failures: open an incident/bug with logs and links to jobs
- Smart routing: tech bugs — Jira; ops tasks — Bitrix24; experiments — ClickUp
- Less boilerplate, easier testing/maintenance, consistent logging and error handling

[Back to top](#contents)

## Examples

### CI/CD failure: create a Jira bug
```php
use GuzzleHttp\Client as GuzzleClient;
use Http\Discovery\Psr17FactoryDiscovery;
use UniversalTaskTracker\Http\Psr18HttpClient;
use UniversalTaskTracker\Core\Config\ArrayConfig;
use UniversalTaskTracker\Core\Registry\DriverRegistry;
use UniversalTaskTracker\Core\TaskTrackerBuilder;
use UniversalTaskTracker\Drivers\Jira\JiraDriver;
use UniversalTaskTracker\Drivers\Jira\Connections\CloudTokenConnection;

$psrReq = Psr17FactoryDiscovery::findRequestFactory();
$psrStr = Psr17FactoryDiscovery::findStreamFactory();
$conn = new CloudTokenConnection([
  'email' => getenv('JIRA_EMAIL'),
  'api_token' => getenv('JIRA_API_TOKEN'),
  'domain' => getenv('JIRA_DOMAIN'),
]);
$http = new Psr18HttpClient(new GuzzleClient(), $psrReq, $psrStr, $conn->getBaseUrl(), $conn->getHeaders());

$registry = new DriverRegistry();
$registry->register('jira', fn() => new JiraDriver(null, $http, $conn));

$config  = new ArrayConfig(['trackers' => ['driver' => 'jira']]);
$manager = (new TaskTrackerBuilder($config, $registry))->build();

$error = getenv('CI_ERROR_MSG') ?: 'Deploy failed';
$job   = getenv('CI_JOB_URL') ?: '';
$payload = [
  'fields' => [
    'project' => ['key' => getenv('JIRA_PROJECT') ?: 'OPS'],
    'issuetype' => ['name' => 'Bug'],
    'summary' => "[Deploy][prod] $error",
    'description' => "Job: $job\n\n$error",
    'labels' => ['ci','deploy'],
  ],
];
$manager->createTask($payload);
```

### Web bug form: route to Jira or Bitrix24
```php
use UniversalTaskTracker\Core\Registry\DriverRegistry;
use UniversalTaskTracker\Core\TaskTrackerBuilder;
use UniversalTaskTracker\Core\Config\ArrayConfig;

$registry = new DriverRegistry();
$registry->register('jira', fn() => $jiraDriver);
$registry->register('bitrix', fn() => $bitrixDriver);

$category = $_POST['category'] ?? 'bug';
$driverName = $category === 'bug' ? 'jira' : 'bitrix';
$config = new ArrayConfig(['trackers' => ['driver' => $driverName]]);
$manager = (new TaskTrackerBuilder($config, $registry))->build();

$data = [
  'fields' => [
    'project' => ['key' => 'WEB'],
    'issuetype' => ['name' => 'Bug'],
    'summary' => $_POST['title'] ?? 'Bug report',
    'description' => ($_POST['description'] ?? '') . "\nURL: " . ($_POST['url'] ?? ''),
    'labels' => ['from_site', 'user_bug'],
  ],
];
$manager->createTask($data);
```

### Slack command: create a ClickUp idea
```php
use UniversalTaskTracker\Drivers\ClickUp\ClickUpDriver;
use UniversalTaskTracker\Drivers\ClickUp\Connections\TokenConnection;

$clickConn = new TokenConnection(getenv('CLICKUP_TOKEN'));
$httpCU = new Psr18HttpClient(new GuzzleClient(), $psrReq, $psrStr, $clickConn->getBaseUrl(), $clickConn->getHeaders());
$driver = new ClickUpDriver(null, $httpCU, $clickConn, getenv('CLICKUP_LIST_ID'));

$registry = new DriverRegistry();
$registry->register('clickup', fn() => $driver);
$config = new ArrayConfig(['trackers' => ['driver' => 'clickup']]);
$manager = (new TaskTrackerBuilder($config, $registry))->build();

$text = $_POST['text'] ?? 'New idea';
$payload = [
  'name' => "[Slack] $text",
  'description' => 'Created from Slack command',
  'labels' => ['idea','slack'],
];
$manager->createTask($payload);
```

### Cross-tracker mirror: Jira to ClickUp
```php
$srcId = 'WEB-123';
$jiraTask = $jiraManager->getTask($srcId);
if ($jiraTask->success) {
  $title = $jiraTask->data['title'] ?? $srcId;
  $clickupManager->createTask([
    'name' => "[Mirror] $title",
    'description' => 'Mirror from Jira',
  ]);
}
```

[Back to top](#contents)

---

## Русский

Фреймворк-агностичный PHP-пакет с единым фасадом для работы с разными API трекеров задач (например, Jira, Bitrix24).

- Унифицированный интерфейс драйверов: createTask, updateTask, deleteTask, getTask
- Драйверы: Jira (Cloud/Server), Bitrix24
- Подключаемый HTTP-клиент (есть адаптер PSR-18), или cURL
- Необязательное логирование в файл
- Мост для Laravel (провайдер, публикация конфига)

## Установка

```bash
composer require dushesss/universal-task-tracker
```

[Наверх](#contents)

## Быстрый старт (Plain PHP)

```php
use UniversalTaskTracker\Facades\TaskTracker;

TaskTracker::use('битрикс');

$response = TaskTracker::createTask(['title' => 'Моя задача']);
if ($response->success) {
    echo $response->data['id'];
}
```

Для реальных вызовов передайте PSR-18 клиент и объект соединения в драйвер (см. примеры в драйверах и Http/Psr18HttpClient).

[Наверх](#contents)

## Laravel

- Провайдер обнаруживается автоматически: `UniversalTaskTracker\Bridge\Laravel\ServiceProvider`.
- Публикация конфига:

```bash
php artisan vendor:publish --tag=task-tracker-config
```

Установите `TRACKER_DRIVER` в `.env`.

[Наверх](#contents)

## Конфигурация (пример)

```php
return [
    'driver' => getenv('TRACKER_DRIVER') ?: 'bitrix',
    'log_path' => getenv('TRACKER_LOG_PATH') ?: __DIR__ . '/../storage/logs/task-tracker.log',
];
```

[Наверх](#contents)

## HTTP-клиент

- Встроенный: `CurlHttpClient`
- Адаптер PSR-18: `Psr18HttpClient` (совместим с Guzzle/Symfony HttpClient и PSR-17 фабриками)

[Наверх](#contents)

## Ответ

```php
class TrackerResponse {
    public bool $success;
    public string $message;
    public ?array $data;
}
```

[Наверх](#contents)

## Как написать свой драйвер

Вы можете добавить поддержку своей CRM/трекера без изменения пакета:

1) Реализуйте драйвер
```php
use UniversalTaskTracker\Contracts\TrackerDriverInterface;
use UniversalTaskTracker\DTO\TrackerResponse;

final class CustomCrmDriver implements TrackerDriverInterface {
    public function createTask(array $data): TrackerResponse { /* ... */ }
    public function updateTask(string $id, array $data): TrackerResponse { /* ... */ }
    public function deleteTask(string $id): TrackerResponse { /* ... */ }
    public function getTask(string $id): TrackerResponse { /* ... */ }
}
```

2) (Опционально) Создайте класс соединения для авторизации/заголовков
```php
final class CustomCrmConnection {
    public function __construct(private string $baseUrl, private string $token) {}
    public function getBaseUrl(): string { return rtrim($this->baseUrl, '/') . '/api/'; }
    public function getHeaders(): array { return ['Authorization' => "Bearer {$this->token}"]; }
}
```

3) Зарегистрируйте драйвер в вашем приложении через `DriverRegistry`
```php
use UniversalTaskTracker\Core\Registry\DriverRegistry;
$registry = new DriverRegistry();
$registry->register('customcrm', fn() => new CustomCrmDriver(/* http, conn, logger */));
```

4) Соберите менеджер с вашим реестром
```php
use UniversalTaskTracker\Core\TaskTrackerBuilder;
use UniversalTaskTracker\Core\Config\ArrayConfig;

$config  = new ArrayConfig(['trackers' => ['driver' => 'customcrm']]);
$builder = new TaskTrackerBuilder($config, $registry);
$manager = $builder->build();
```

В Laravel это можно сделать в своём провайдере приложения, затем вызвать `TaskTracker::setManager($manager)`.

[Наверх](#contents)

## Use cases — зачем нужен пакет

- Один универсальный API вместо «зоопарка» SDK (Jira, Bitrix24, ClickUp, Asana и т.д.)
- Лёгкое подключение собственного драйвера для внутренней CRM
- Форма «Сообщить об ошибке» на сайте: создаёт тикет со скриншотом/URL/браузером
- «Предложить улучшение»: уходит в нужный проект с метками и приоритетом
- Падения в CI/CD: создаётся задача с логами и ссылками на джобы
- Маршрутизация: техбаги — в Jira; операционные задачи — в Bitrix24; идеи — в ClickUp
- Меньше шаблонного кода, проще тестирование/поддержка, единое логирование и обработка ошибок

[Наверх](#contents)

## Примеры

### Падение CI/CD: создать баг в Jira
См. пример: [CI/CD failure: create a Jira bug](#cicd-failure-create-a-jira-bug)

### Веб-форма баг-репорта: маршрутизация в Jira или Bitrix24
См. пример: [Web bug form: route to Jira or Bitrix24](#web-bug-form-route-to-jira-or-bitrix24)

### Команда в Slack: создать идею в ClickUp
См. пример: [Slack command: create a ClickUp idea](#slack-command-create-a-clickup-idea)

### Зеркалирование между трекерами: Jira -> ClickUp
См. пример: [Cross-tracker mirror: Jira to ClickUp](#cross-tracker-mirror-jira-to-clickup)

[Наверх](#contents)
