<?php

declare(strict_types=1);

namespace App\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class StdOutLogger extends AbstractLogger
{
    public function __construct(private bool $debug)
    {
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        if (!$this->debug && $level === LogLevel::DEBUG) {
            return;
        }

        $resource = fopen('php://stdout', 'w');
        @fwrite($resource, "{$level} {$message} context: " . json_encode($context) . "\n");
        @fclose($resource);
    }
}