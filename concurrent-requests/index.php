<?php declare(strict_types=1);

use Swoole\Coroutine;

function request(): string
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://mockbin.org/delay/2000',
        CURLOPT_RETURNTRANSFER => true,
    ]);

    return curl_exec($ch);
}

Coroutine\run(static function (): void {
    $responses = [];
    $wg = new Coroutine\WaitGroup();

    Coroutine\parallel(10, static function() use ($wg, &$responses): void {
        $wg->add();
        $responses[] = request();
        $wg->done();
    });

    $wg->wait();

    var_dump($responses);
});
