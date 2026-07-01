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
     * Test that the factory returns a GeneratedAtMiddleware instance when invoked with a service manager container.
     */
    public function testFactoryReturnsExpectedClassName(): void
    {
        $container = new ServiceManager();

        $actual = $this->generatedAtMiddlewareFactory->__invoke($container);

        self::assertSame(GeneratedAtMiddleware::class, $actual::class);
    }

    /**
     * Test that the factory returns a distinct middleware instance when invoked more than once.
     */
    public function testFactoryCreatesNewInstanceOnEachInvocation(): void
    {
        $container = new ServiceManager();

        $instance1 = $this->generatedAtMiddlewareFactory->__invoke($container);
        $instance2 = $this->generatedAtMiddlewareFactory->__invoke($container);

        self::assertNotSame($instance1, $instance2);
    }

    /**
     * Test that the factory returns a GeneratedAtMiddleware instance when invoked with any PSR-11 container.
     */
    public function testFactoryWorksWithPsr11Container(): void
    {
        $container = self::createStub(ContainerInterface::class);

        $actual = $this->generatedAtMiddlewareFactory->__invoke($container);

        self::assertSame(GeneratedAtMiddleware::class, $actual::class);
    }

    /**
     * Test that the factory returns a GeneratedAtMiddleware instance when invoked using direct callable syntax.
     */
    public function testFactoryInvocationViaCallableSyntax(): void
    {
        $container = new ServiceManager();

        $result = ($this->generatedAtMiddlewareFactory)($container);

        self::assertSame(GeneratedAtMiddleware::class, $result::class);
    }
}
