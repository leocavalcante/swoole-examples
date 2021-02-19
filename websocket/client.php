<?php declare(strict_types=1);

namespace Example\WebSocket;

use Swoole\Coroutine;
use Swoole\Coroutine\Http\Client;
use Swoole\WebSocket\Frame;

require_once __DIR__ . '/../vendor/autoload.php';

function on_frame(Client $client, Frame $frame): void
{
    $simulate_work = random_int(1, 5);

    Coroutine::sleep($simulate_work);

    echo time() . " (client) x $frame->data (server) :: $simulate_work\n";
}

Coroutine\run(static function (): void {
    $client = new Client('127.0.0.1', 3000);
    $client->upgrade('/');

    while (true) {
        if ($frame = $client->recv()) {
            Coroutine::create(static function() use ($client, $frame): void {
                on_frame($client, $frame);
            });
        }
    }
});



