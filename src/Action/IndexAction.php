<?php

declare(strict_types=1);

namespace App\Action;

use App\Message\IncomingGpx;
use Psr\Log\LoggerInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

class IndexAction
{
    private Server $server;

    private LoggerInterface $logger;

    public function __construct(Server $server, LoggerInterface $logger
    ) {
        $this->server = $server;
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response): void
    {
        // Define directories
        $resourcesDir = 'resources';
        $processedDir = 'resources/processed';
        $processingDir = 'resources/processing';

// Create HTML header
        $responseContent = '<!DOCTYPE html>
<html>
<head>
    <title>File Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <h1>File Dashboard</h1>
    <h2>Resources Directory</h2>
    <table>
        <tr>
            <th>File Name</th>
            <th>Last Modified</th>
        </tr>';

// List files in resources directory
        $files = scandir($resourcesDir) ?: [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filePath = $resourcesDir . '/' . $file;
                $lastModified = date('Y-m-d H:i:s', filemtime($filePath));
                $responseContent .= '<tr>
            <td>' . $file . '</td>
            <td>' . $lastModified . '</td>
        </tr>';
            }
        }

// List files in resources/processed directory
        $responseContent .= '</table>
    <h2>Processed Directory</h2>
    <table>
        <tr>
            <th>File Name</th>
            <th>Last Modified</th>
        </tr>';

        $files = scandir($processedDir) ?: [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filePath = $processedDir . '/' . $file;
                $lastModified = date('Y-m-d H:i:s', filemtime($filePath));
                $responseContent .= '<tr>
            <td>' . $file . '</td>
            <td>' . $lastModified . '</td>
        </tr>';
            }
        }

// List files in resources/processing directory
        $responseContent .= '</table>
    <h2>Processing Directory</h2>
    <table>
        <tr>
            <th>File Name</th>
            <th>Last Modified</th>
        </tr>';

        $files = scandir($processingDir) ?: [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filePath = $processingDir . '/' . $file;
                $lastModified = date('Y-m-d H:i:s', filemtime($filePath));
                $responseContent .= '<tr>
            <td>' . $file . '</td>
            <td>' . $lastModified . '</td>
        </tr>';
            }
        }

// Close HTML tags
        $responseContent .= '</table>
</body>
</html>';

        $response->write($responseContent);
    }
}
