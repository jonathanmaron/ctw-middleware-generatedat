<?php
declare(strict_types=1);

namespace Ctw\Middleware\GeneratedAtMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GeneratedAtMiddleware extends AbstractGeneratedAtMiddleware
{
    private const string HEADER = 'X-Generated-At';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $server = $request->getServerParams();
        $timestamp = $server['REQUEST_TIME_FLOAT'] ?? microtime(false);

        $response    = $handler->handle($request);
        $generatedAt = gmdate('Y-m-d\TH:i:s\Z', (int) $timestamp);

        return $response->withHeader(self::HEADER, $generatedAt);
    }
}
