<?php
declare(strict_types=1);

namespace CtwTest\Middleware\GeneratedAtMiddleware;

use Ctw\Middleware\GeneratedAtMiddleware\GeneratedAtMiddleware;
use Ctw\Middleware\GeneratedAtMiddleware\GeneratedAtMiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;

class GeneratedAtMiddlewareTest extends AbstractCase
{
    public function testGeneratedAtMiddleware(): void
    {
        $timestamp   = (int) microtime(false);
        $generatedAt = gmdate('Y-m-d\TH:i:s\Z', $timestamp);

        $serverParams = [
            'REQUEST_TIME_FLOAT' => $timestamp,
        ];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [
            $this->getInstance(),
        ];
        $response     = Dispatcher::run($stack, $request);

        $this->assertEquals($generatedAt, $response->getHeaderLine('X-Generated-At'));
    }

    public function testGeneratedAtMiddlewareMicrotime(): void
    {
        $timestamp   = (int) microtime(false);
        $generatedAt = gmdate('Y-m-d\TH:i:s\Z', $timestamp);

        $serverParams = [];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [
            $this->getInstance(),
        ];
        $response     = Dispatcher::run($stack, $request);

        $this->assertEquals($generatedAt, $response->getHeaderLine('X-Generated-At'));
    }

    private function getInstance(): GeneratedAtMiddleware
    {
        $container = new ServiceManager();
        $factory   = new GeneratedAtMiddlewareFactory();

        return $factory->__invoke($container);
    }
}
