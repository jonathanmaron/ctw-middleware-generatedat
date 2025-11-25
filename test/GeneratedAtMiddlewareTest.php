<?php

declare(strict_types=1);

namespace CtwTest\Middleware\GeneratedAtMiddleware;

use Ctw\Middleware\GeneratedAtMiddleware\GeneratedAtMiddleware;
use Ctw\Middleware\GeneratedAtMiddleware\GeneratedAtMiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\Attributes\DataProvider;

final class GeneratedAtMiddlewareTest extends AbstractCase
{
    private GeneratedAtMiddleware $generatedAtMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generatedAtMiddleware = $this->createMiddlewareInstance();
    }

    /**
     * Test that response contains X-Generated-At header
     */
    public function testResponseContainsGeneratedAtHeader(): void
    {
        $serverParams = [
            'REQUEST_TIME_FLOAT' => 1700000000.123456,
        ];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [$this->generatedAtMiddleware];

        $response = Dispatcher::run($stack, $request);

        self::assertTrue($response->hasHeader('X-Generated-At'));
    }

    /**
     * Test that timestamp is correctly formatted from REQUEST_TIME_FLOAT
     *
     * @return array<string, array{timestamp: float|int, expected: string}>
     */
    public static function timestampProvider(): array
    {
        return [
            'unix epoch'           => [
                'timestamp' => 0,
                'expected'  => '1970-01-01T00:00:00Z',
            ],
            'integer timestamp'    => [
                'timestamp' => 1700000000,
                'expected'  => '2023-11-14T22:13:20Z',
            ],
            'float timestamp'      => [
                'timestamp' => 1700000000.123456,
                'expected'  => '2023-11-14T22:13:20Z',
            ],
            'y2k timestamp'        => [
                'timestamp' => 946684800,
                'expected'  => '2000-01-01T00:00:00Z',
            ],
            'negative timestamp'   => [
                'timestamp' => -86400,
                'expected'  => '1969-12-31T00:00:00Z',
            ],
            'float with decimals'  => [
                'timestamp' => 1609459200.999999,
                'expected'  => '2021-01-01T00:00:00Z',
            ],
        ];
    }

    /**
     * Test that various timestamps are formatted correctly
     */
    #[DataProvider('timestampProvider')]
    public function testTimestampFormattingWithRequestTimeFloat(int|float $timestamp, string $expected): void
    {
        $serverParams = [
            'REQUEST_TIME_FLOAT' => $timestamp,
        ];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [$this->generatedAtMiddleware];

        $response = Dispatcher::run($stack, $request);
        $actual   = $response->getHeaderLine('X-Generated-At');

        self::assertSame($expected, $actual);
    }

    /**
     * Test that middleware falls back to microtime when REQUEST_TIME_FLOAT is missing
     */
    public function testFallbackToMicrotimeWhenRequestTimeFloatMissing(): void
    {
        $timeBefore = time();

        $serverParams = [];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [$this->generatedAtMiddleware];

        $response = Dispatcher::run($stack, $request);

        $timeAfter   = time();
        $headerValue = $response->getHeaderLine('X-Generated-At');

        // Verify header format matches ISO 8601 pattern
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $headerValue);

        // Verify timestamp is within the expected range
        $headerTimestamp = strtotime($headerValue);
        self::assertGreaterThanOrEqual($timeBefore, $headerTimestamp);
        self::assertLessThanOrEqual($timeAfter, $headerTimestamp);
    }

    /**
     * Test that header value format is ISO 8601 UTC
     */
    public function testHeaderValueFormatIsIso8601Utc(): void
    {
        $serverParams = [
            'REQUEST_TIME_FLOAT' => 1700000000,
        ];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [$this->generatedAtMiddleware];

        $response    = Dispatcher::run($stack, $request);
        $headerValue = $response->getHeaderLine('X-Generated-At');

        // Verify ISO 8601 UTC format (YYYY-MM-DDTHH:MM:SSZ)
        self::assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $headerValue);
    }

    /**
     * Test that middleware processes different HTTP methods
     */
    #[DataProvider('httpMethodProvider')]
    public function testMiddlewareProcessesDifferentHttpMethods(string $method): void
    {
        $serverParams = [
            'REQUEST_TIME_FLOAT' => 1700000000,
        ];
        $request      = Factory::createServerRequest($method, '/', $serverParams);
        $stack        = [$this->generatedAtMiddleware];

        $response = Dispatcher::run($stack, $request);

        self::assertTrue($response->hasHeader('X-Generated-At'));
        self::assertSame('2023-11-14T22:13:20Z', $response->getHeaderLine('X-Generated-At'));
    }

    /**
     * Provides HTTP methods for testing
     *
     * @return array<string, array{method: string}>
     */
    public static function httpMethodProvider(): array
    {
        return [
            'GET'     => [
                'method' => 'GET',
            ],
            'POST'    => [
                'method' => 'POST',
            ],
            'PUT'     => [
                'method' => 'PUT',
            ],
            'DELETE'  => [
                'method' => 'DELETE',
            ],
            'PATCH'   => [
                'method' => 'PATCH',
            ],
            'OPTIONS' => [
                'method' => 'OPTIONS',
            ],
            'HEAD'    => [
                'method' => 'HEAD',
            ],
        ];
    }

    /**
     * Test that middleware works with different URI paths
     */
    #[DataProvider('uriPathProvider')]
    public function testMiddlewareWorksWithDifferentUriPaths(string $path): void
    {
        $serverParams = [
            'REQUEST_TIME_FLOAT' => 1700000000,
        ];
        $request      = Factory::createServerRequest('GET', $path, $serverParams);
        $stack        = [$this->generatedAtMiddleware];

        $response = Dispatcher::run($stack, $request);

        self::assertTrue($response->hasHeader('X-Generated-At'));
    }

    /**
     * Provides URI paths for testing
     *
     * @return array<string, array{path: string}>
     */
    public static function uriPathProvider(): array
    {
        return [
            'root path'        => [
                'path' => '/',
            ],
            'simple path'      => [
                'path' => '/api',
            ],
            'nested path'      => [
                'path' => '/api/v1/users',
            ],
            'path with query'  => [
                'path' => '/search?q=test',
            ],
            'path with anchor' => [
                'path' => '/page#section',
            ],
        ];
    }

    /**
     * Test that integer timestamp in REQUEST_TIME_FLOAT is handled
     */
    public function testIntegerTimestampIsHandled(): void
    {
        $timestamp    = 1700000000;
        $serverParams = [
            'REQUEST_TIME_FLOAT' => $timestamp,
        ];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [$this->generatedAtMiddleware];

        $response = Dispatcher::run($stack, $request);
        $expected = gmdate('Y-m-d\TH:i:s\Z', $timestamp);

        self::assertSame($expected, $response->getHeaderLine('X-Generated-At'));
    }

    /**
     * Test that float timestamp in REQUEST_TIME_FLOAT is handled
     */
    public function testFloatTimestampIsHandled(): void
    {
        $timestamp    = 1700000000.123456;
        $serverParams = [
            'REQUEST_TIME_FLOAT' => $timestamp,
        ];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [$this->generatedAtMiddleware];

        $response = Dispatcher::run($stack, $request);
        $expected = gmdate('Y-m-d\TH:i:s\Z', (int) $timestamp);

        self::assertSame($expected, $response->getHeaderLine('X-Generated-At'));
    }

    /**
     * Create middleware instance via factory
     */
    private function createMiddlewareInstance(): GeneratedAtMiddleware
    {
        $container = new ServiceManager();
        $factory   = new GeneratedAtMiddlewareFactory();

        return $factory->__invoke($container);
    }
}
