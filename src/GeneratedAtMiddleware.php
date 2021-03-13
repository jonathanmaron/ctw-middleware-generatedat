<?php
declare(strict_types=1);

namespace Ctw\Middleware\GeneratedAtMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GeneratedAtMiddleware extends AbstractGeneratedAtMiddleware
{
    private const HEADER = 'X-Generated-At';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response    = $handler->handle($request);
        $server      = $request->getServerParams();
        $timestamp   = (int) $server['REQUEST_TIME_FLOAT'] ?? microtime(false);
        $generatedAt = gmdate('Y-m-d\TH:i:s\Z', $timestamp);

        return $response->withHeader(self::HEADER, $generatedAt);
    }
}
