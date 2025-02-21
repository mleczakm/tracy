<?php

declare(strict_types=1);

namespace App\Message;

final class IncomingGpx
{
    public function __construct(public string $path)
    {}
}
