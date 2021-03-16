<?php declare(strict_types=1);

namespace Example\TaskWorkers;

use Swoole\Constant;
use Swoole\Server;

$wid = -1;
$subscribers = [];

$server = new Server('0.0.0.0', 9000, SWOOLE_BASE, SWOOLE_TCP);

$server->on('workerStart', static function(Server $server, int $worker_id) use (&$wid, &$subscribers): void {
    $wid = $worker_id;
    /**
     * Ids higher than the worker_num-1 indicates that are Task Workers
     */
    if ($worker_id > 0) {
        swoole_set_process_name("Task Worker $worker_id");

        while (true) {
            foreach ($subscribers as $fd) {
                $server->send($fd, time() . PHP_EOL);
            }
            sleep(1);
        }
    }
});

$server->on('task', static function (Server $server, Server\Task $task) use (&$wid, &$subscribers): bool {
    ['fd' => $fd] = $task->data;
    echo "New task ({$task->id}) handled by {$wid} from #{$fd}\n";

    switch ($task->data['type']) {
        case 'subscribe':
            echo "Subscribing #{$fd}\n";
            $subscribers[$fd] = $fd;
            break;

        case 'unsubscribe':
            echo "Unsubscribing #{$fd}\n";
            unset($subscribers[$fd]);
            break;
    }

    return true;
});

$server->on('connect', static function(Server $server, int $fd): void {
    echo "Hello #$fd\n";
    $server->task(['type' => 'subscribe', 'fd' => $fd]);
});

$server->on('close', static function (Server $server, int $fd): void {
    echo "#$fd is leaving\n";
    $server->task(['type' => 'unsubscribe', 'fd' => $fd]);
});

$server->on('receive', static function(): void {});

$server->set([
    Constant::OPTION_WORKER_NUM => 1,
    Constant::OPTION_TASK_WORKER_NUM => 2,
    Constant::OPTION_TASK_ENABLE_COROUTINE => true,
]);

$server->start();
