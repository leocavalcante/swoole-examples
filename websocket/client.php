<?php declare(strict_types=1);

namespace Example\WebSocket;

use Swoole\Coroutine;
use Swoole\Coroutine\Http\Client;
use Swoole\WebSocket\Frame;

require_once __DIR__ . '/../vendor/autoload.php';

function on_frame(Client $client, Frame $frame): void
{
    echo "$frame->data\n";
    $client->push($frame->data);
}

Coroutine\run(static function (): void {
    $client = new Client('127.0.0.1', 3000);
    $client->upgrade('/');

    while (true) {
        if ($frame = $client->recv()) {
            on_frame($client, $frame);
        }
    }
});



