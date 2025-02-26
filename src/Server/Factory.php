<?php

declare(strict_types=1);

namespace App\Server;

use Swoole\Http\Server;

class Factory
{
    public static function create(string $environment): Server
    {
        $server = new Server('0.0.0.0', $environment === 'dev' ? 8080 : 80, SWOOLE_BASE);
        $server->set([
            'worker_num' => 4,
            'task_worker_num' => 4,
            'enable_static_handler' => true,
            'document_root' => __DIR__ .'/../../public',
            'http_parse_post' => false,
            'task_enable_coroutine' => true,
        ]);

        return $server;
    }
}
