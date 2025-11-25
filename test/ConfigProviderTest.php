<?php

declare(strict_types=1);

namespace CtwTest\Middleware\GeneratedAtMiddleware;

use Ctw\Middleware\GeneratedAtMiddleware\ConfigProvider;
use Ctw\Middleware\GeneratedAtMiddleware\GeneratedAtMiddleware;
use Ctw\Middleware\GeneratedAtMiddleware\GeneratedAtMiddlewareFactory;

final class ConfigProviderTest extends AbstractCase
{
    private ConfigProvider $configProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configProvider = new ConfigProvider();
    }

    /**
     * Test that invocation returns complete configuration array
     */
    public function testInvokeReturnsConfigurationArray(): void
    {
        $expected = [
            'dependencies' => [
                'factories' => [
                    GeneratedAtMiddleware::class => GeneratedAtMiddlewareFactory::class,
                ],
            ],
        ];

        $actual = ($this->configProvider)();

        self::assertSame($expected, $actual);
    }

    /**
     * Test that configuration contains dependencies key
     */
    public function testInvokeReturnsDependenciesKey(): void
    {
        $config = ($this->configProvider)();

        self::assertArrayHasKey('dependencies', $config);
    }

    /**
     * Test that getDependencies returns factory mappings
     */
    public function testGetDependenciesReturnsFactoryMappings(): void
    {
        $expected = [
            'factories' => [
                GeneratedAtMiddleware::class => GeneratedAtMiddlewareFactory::class,
            ],
        ];

        $actual = $this->configProvider->getDependencies();

        self::assertSame($expected, $actual);
    }

    /**
     * Test that getDependencies contains factories key
     */
    public function testGetDependenciesContainsFactoriesKey(): void
    {
        $dependencies = $this->configProvider->getDependencies();

        self::assertArrayHasKey('factories', $dependencies);
    }

    /**
     * Test that middleware class is registered in factories
     */
    public function testMiddlewareIsRegisteredInFactories(): void
    {
        $dependencies = $this->configProvider->getDependencies();
        $factories    = $dependencies['factories'];
        self::assertIsArray($factories);

        self::assertArrayHasKey(GeneratedAtMiddleware::class, $factories);
    }

    /**
     * Test that middleware factory is correctly mapped
     */
    public function testMiddlewareFactoryIsCorrectlyMapped(): void
    {
        $dependencies = $this->configProvider->getDependencies();
        $factories    = $dependencies['factories'];
        self::assertIsArray($factories);

        self::assertSame(GeneratedAtMiddlewareFactory::class, $factories[GeneratedAtMiddleware::class]);
    }
}
