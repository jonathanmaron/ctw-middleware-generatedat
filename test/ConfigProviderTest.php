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
     * Test that __invoke() returns the complete configuration array when the provider is called.
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
     * Test that __invoke() includes the dependencies key when it returns the configuration array.
     */
    public function testInvokeReturnsDependenciesKey(): void
    {
        $config = ($this->configProvider)();

        self::assertArrayHasKey('dependencies', $config);
    }

    /**
     * Test that getDependencies() returns the expected factory mappings when called.
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
     * Test that getDependencies() includes the factories key when called.
     */
    public function testGetDependenciesContainsFactoriesKey(): void
    {
        $dependencies = $this->configProvider->getDependencies();

        self::assertArrayHasKey('factories', $dependencies);
    }

    /**
     * Test that getDependencies() registers the middleware class as a factory key when called.
     */
    public function testMiddlewareIsRegisteredInFactories(): void
    {
        $dependencies = $this->configProvider->getDependencies();
        $factories    = $dependencies['factories'];
        self::assertIsArray($factories);

        self::assertArrayHasKey(GeneratedAtMiddleware::class, $factories);
    }

    /**
     * Test that getDependencies() maps the middleware to its factory class when called.
     */
    public function testMiddlewareFactoryIsCorrectlyMapped(): void
    {
        $dependencies = $this->configProvider->getDependencies();
        $factories    = $dependencies['factories'];
        self::assertIsArray($factories);

        self::assertSame(GeneratedAtMiddlewareFactory::class, $factories[GeneratedAtMiddleware::class]);
    }
}
