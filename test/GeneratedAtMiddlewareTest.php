<?php

declare(strict_types=1);

namespace CtwTest\Middleware\GeneratedAtMiddleware;

use AssertionError;
use Ctw\Middleware\AbstractMiddleware;
use Ctw\Middleware\GeneratedAtMiddleware\AbstractGeneratedAtMiddleware;
use Ctw\Middleware\GeneratedAtMiddleware\GeneratedAtMiddleware;
use Ctw\Middleware\GeneratedAtMiddleware\GeneratedAtMiddlewareFactory;
use Laminas\ServiceManager\ServiceManager;
use Middlewares\Utils\Dispatcher;
use Middlewares\Utils\Factory;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

final class GeneratedAtMiddlewareTest extends AbstractCase
{
    private GeneratedAtMiddleware $generatedAtMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generatedAtMiddleware = $this->createMiddlewareInstance();
    }

    /**
     * Test that process() adds the X-Generated-At header to the response when the middleware handles a request.
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
     * Provides REQUEST_TIME_FLOAT timestamps paired with their expected ISO 8601 UTC header output.
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
     * Test that process() formats the X-Generated-At value as ISO 8601 UTC when REQUEST_TIME_FLOAT supplies the timestamp.
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
     * Test that process() falls back to the current time when REQUEST_TIME_FLOAT is absent from the server params.
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
     * Test that process() produces an ISO 8601 UTC formatted header value when an integer REQUEST_TIME_FLOAT is provided.
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
     * Test that process() adds the X-Generated-At header when the request uses any standard HTTP method.
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
     * Test that process() adds the X-Generated-At header when the request targets any URI path.
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
     * Test that process() formats the header value correctly when REQUEST_TIME_FLOAT is an integer.
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
     * Test that process() truncates the fractional part to whole seconds when REQUEST_TIME_FLOAT is a float.
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
     * Test that the middleware extends the package and framework abstract base classes when built by the factory.
     */
    public function testMiddlewareExtendsAbstractBaseClasses(): void
    {
        $parents    = class_parents(GeneratedAtMiddleware::class);
        $implements = class_implements(GeneratedAtMiddleware::class);
        self::assertIsArray($parents);
        self::assertIsArray($implements);

        self::assertArrayHasKey(AbstractGeneratedAtMiddleware::class, $parents);
        self::assertArrayHasKey(AbstractMiddleware::class, $parents);
        self::assertArrayHasKey(MiddlewareInterface::class, $implements);
    }

    /**
     * Test that process() preserves the downstream response status code and body when it appends the header.
     */
    public function testProcessPreservesDownstreamResponseStatusAndBody(): void
    {
        $handler = static function (): ResponseInterface {
            $response = Factory::createResponse(418);
            $response->getBody()
                ->write('short and stout');

            return $response;
        };

        $serverParams = [
            'REQUEST_TIME_FLOAT' => 1700000000,
        ];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [$this->generatedAtMiddleware, $handler];

        $response = Dispatcher::run($stack, $request);

        self::assertSame(418, $response->getStatusCode());
        self::assertSame('short and stout', (string) $response->getBody());
        self::assertSame('2023-11-14T22:13:20Z', $response->getHeaderLine('X-Generated-At'));
    }

    /**
     * Test that process() replaces any pre-existing X-Generated-At header when the downstream response already set one.
     */
    public function testProcessReplacesExistingGeneratedAtHeader(): void
    {
        $handler = static fn (): ResponseInterface => Factory::createResponse()
            ->withHeader('X-Generated-At', 'STALE-VALUE');

        $serverParams = [
            'REQUEST_TIME_FLOAT' => 1700000000,
        ];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [$this->generatedAtMiddleware, $handler];

        $response = Dispatcher::run($stack, $request);

        self::assertCount(1, $response->getHeader('X-Generated-At'));
        self::assertSame('2023-11-14T22:13:20Z', $response->getHeaderLine('X-Generated-At'));
    }

    /**
     * Test that process() fails the numeric type assertion when REQUEST_TIME_FLOAT holds a non-numeric value.
     */
    public function testProcessTriggersAssertionFailureWhenRequestTimeFloatIsNonNumeric(): void
    {
        if ('1' !== (string) ini_get('zend.assertions')) {
            self::markTestSkipped('Runtime assertions are not active.');
        }

        $serverParams = [
            'REQUEST_TIME_FLOAT' => 'not-a-number',
        ];
        $request      = Factory::createServerRequest('GET', '/', $serverParams);
        $stack        = [$this->generatedAtMiddleware];

        $this->expectException(AssertionError::class);

        Dispatcher::run($stack, $request);
    }

    /**
     * Create a middleware instance through its factory for use as the subject under test.
     */
    private function createMiddlewareInstance(): GeneratedAtMiddleware
    {
        $container = new ServiceManager();
        $factory   = new GeneratedAtMiddlewareFactory();

        return $factory->__invoke($container);
    }
}
