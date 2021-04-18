<?php declare(strict_types=1);

use Swoole\Coroutine;

require_once __DIR__ . '/../vendor/autoload.php';

Coroutine\run(static function (): void {
    Coroutine::create(fn() => new \GuzzleHttp\Client()); // Found here
    Coroutine::create(fn() => new \GuzzleHttp\Client()); // Not found here (Uncaught Error: Class "GuzzleHttp\Client" not found)
});
