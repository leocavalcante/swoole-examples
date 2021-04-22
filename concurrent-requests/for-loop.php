<?php declare(strict_types=1);

use GuzzleHttp\Client;
use Swoole\Coroutine as Co;
use Swoole\Runtime;

require_once __DIR__ . '/../vendor/autoload.php';

Runtime::enableCoroutine();

Co\run(static function(): void {
    foreach (range(1, 10) as $i) {
        Co::create(static function () use ($i): void {
            $client = new Client(['base_uri' => 'https://httpbin.org']);
            $response = $client->get("/anything?j=$i");
            $data = json_decode($response->getBody()->getContents(), true);

            $j = (int) $data['args']['j'];
            assert($j === $i);
            echo "$i + $j = " . ($i + $j) . PHP_EOL;
        });
    }
});
