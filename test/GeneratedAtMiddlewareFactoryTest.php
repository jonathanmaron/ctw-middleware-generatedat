<?php

declare(strict_types=1);

namespace CtwTest\Middleware\GeneratedAtMiddleware;

use Ctw\Middleware\GeneratedAtMiddleware\GeneratedAtMiddleware;
use Ctw\Middleware\GeneratedAtMiddleware\GeneratedAtMiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

final class GeneratedAtMiddlewareFactoryTest extends AbstractCase
{
    private GeneratedAtMiddlewareFactory $generatedAtMiddlewareFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generatedAtMiddlewareFactory = new GeneratedAtMiddlewareFactory();
    }

    /**
     * Test that factory returns expected class name
     */
    public function testFactoryReturnsExpectedClassName(): void
    {
        $container = new ServiceManager();

        $actual = $this->generatedAtMiddlewareFactory->__invoke($container);

        self::assertSame(GeneratedAtMiddleware::class, $actual::class);
    }

    /**
     * Test that factory creates new instance on each invocation
     */
    public function testFactoryCreatesNewInstanceOnEachInvocation(): void
    {
        $container = new ServiceManager();

        $instance1 = $this->generatedAtMiddlewareFactory->__invoke($container);
        $instance2 = $this->generatedAtMiddlewareFactory->__invoke($container);

        self::assertNotSame($instance1, $instance2);
    }

    /**
     * Test that factory works with any PSR-11 container
     */
    public function testFactoryWorksWithPsr11Container(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $actual = $this->generatedAtMiddlewareFactory->__invoke($container);

        self::assertSame(GeneratedAtMiddleware::class, $actual::class);
    }

    /**
     * Test that factory invocation via callable syntax works
     */
    public function testFactoryInvocationViaCallableSyntax(): void
    {
        $container = new ServiceManager();

        $result = ($this->generatedAtMiddlewareFactory)($container);

        self::assertSame(GeneratedAtMiddleware::class, $result::class);
    }
}
