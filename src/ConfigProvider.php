<?php
declare(strict_types=1);

namespace Ctw\Middleware\GeneratedAtMiddleware;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                GeneratedAtMiddleware::class => GeneratedAtMiddlewareFactory::class,
            ],
        ];
    }
}
