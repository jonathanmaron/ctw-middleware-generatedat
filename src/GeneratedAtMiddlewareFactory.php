<?php
declare(strict_types=1);

namespace Ctw\Middleware\GeneratedAtMiddleware;

use Psr\Container\ContainerInterface;

class GeneratedAtMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): GeneratedAtMiddleware
    {
        return new GeneratedAtMiddleware();
    }
}
