
# Universal Task Tracker

English | [Русский](#русский)

A framework-agnostic PHP package that provides a unified API for working with task trackers like Jira and Bitrix24.

- Unified driver interface: createTask, updateTask, deleteTask, getTask
- Drivers: Jira (Cloud/Server), Bitrix24
- Pluggable HTTP client (PSR-18 adapter provided), custom or cURL
- Optional file logging
- Laravel bridge included (config publish, provider)

## Installation

```bash
composer require dushesss/universal-task-tracker
```

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

## Laravel

- The service provider is auto-discovered: `UniversalTaskTracker\Bridge\Laravel\ServiceProvider`.
- Publish config:

```bash
php artisan vendor:publish --tag=task-tracker-config
```

Set `TRACKER_DRIVER` in your `.env`.

## Configuration (example)

```php
return [
    'driver' => getenv('TRACKER_DRIVER') ?: 'bitrix',
    'log_path' => getenv('TRACKER_LOG_PATH') ?: __DIR__ . '/../storage/logs/task-tracker.log',
];
```

## HTTP Client

- Built-in: `CurlHttpClient`
- PSR-18 adapter: `Psr18HttpClient` (use with Guzzle/Symfony HttpClient and PSR-17 factories)

## Response

```php
class TrackerResponse {
    public bool $success;
    public string $message;
    public ?array $data;
}
```

## Testing

```bash
vendor/bin/phpunit
```

---

## Русский

Фреймворк-агностичный PHP-пакет с унифицированным API для работы с трекерами задач (Jira, Bitrix24).

- Унифицированный интерфейс драйверов: createTask, updateTask, deleteTask, getTask
- Драйверы: Jira (Cloud/Server), Bitrix24
- Подключаемый HTTP-клиент (есть адаптер PSR-18), или cURL
- Необязательное логирование в файл
- Мост для Laravel (провайдер, публикация конфига)

## Установка

```bash
composer require dushesss/universal-task-tracker
```

## Быстрый старт (Plain PHP)

```php
use UniversalTaskTracker\Facades\TaskTracker;

TaskTracker::use('bitrix');

$response = TaskTracker::createTask(['title' => 'Моя задача']);
if ($response->success) {
    echo $response->data['id'];
}
```

Для реальных вызовов передайте PSR-18 клиент и объект соединения в драйвер (см. примеры в драйверах и Http/Psr18HttpClient).

## Laravel

- Провайдер обнаруживается автоматически: `UniversalTaskTracker\Bridge\Laravel\ServiceProvider`.
- Публикация конфига:

```bash
php artisan vendor:publish --tag=task-tracker-config
```

Установите `TRACKER_DRIVER` в `.env`.

## Конфигурация (пример)

```php
return [
    'driver' => getenv('TRACKER_DRIVER') ?: 'bitrix',
    'log_path' => getenv('TRACKER_LOG_PATH') ?: __DIR__ . '/../storage/logs/task-tracker.log',
];
```

## HTTP-клиент

- Встроенный: `CurlHttpClient`
- Адаптер PSR-18: `Psr18HttpClient` (совместим с Guzzle/Symfony HttpClient и PSR-17 фабриками)

## Ответ

```php
class TrackerResponse {
    public bool $success;
    public string $message;
    public ?array $data;
}
```

## Тесты

```bash
vendor/bin/phpunit
```
