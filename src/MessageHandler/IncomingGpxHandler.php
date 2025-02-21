<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\IncomingGpx;
use Psr\Log\LoggerInterface;
use function Flow\ETL\DSL\{data_frame, ref, to_stdout, to_stream};
use function Flow\ETL\Adapter\XML\from_xml;

class IncomingGpxHandler
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(IncomingGpx $incomingGpx): void
    {
        $this->logger->info('Incoming GPX: ' . $incomingGpx->path);

        $counter = 0;

        data_frame()
            ->read(from_xml('resources/processing/'.$incomingGpx->path, 'gpx'))
            ->withEntry('id', ref('node')->domNodeAttribute('id'))
            ->withEntry('lat', ref('node')->domNodeAttribute('lat'))
            ->withEntry('long', ref('node')->domNodeAttribute('long'))
            ->withEntry('ele', ref('node')->xpath('ele')->domNodeValue())
            ->drop('node')
            ->map(function($row) use (&$counter): array {
                ++$counter;
                //do something with every row

                return $row;
            })
            ->map(function($row) use (&$counter): array {
                ++$counter;
                //do something again with every row

                return $row;
            })
            ->display()
            ;

        rename('resources/processing/'.$incomingGpx->path, 'resources/processed' . $incomingGpx->path);

        $this->logger->info('Incoming GPX handled: ' . $incomingGpx->path);
    }
}
