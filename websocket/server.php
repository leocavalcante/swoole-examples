<?php declare(strict_types=1);

namespace Example\WebSocket;

use Swoole\Constant;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

require_once __DIR__ . '/../vendor/autoload.php';

$server = new Server('127.0.0.1', 3000);
$server->set([Constant::OPTION_WORKER_NUM => 1]);

$server->on('open', static function (Server $server, Request $request): void {
    $server->tick(1000, static function () use ($server, $request): void {
        $server->push($request->fd, time());
    });
});

$server->on('message', function (Server $server, Frame $frame): void {
    echo "Received: {$frame->data}\n";
});

$server->start();
