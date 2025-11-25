# Package "ctw/ctw-middleware-generatedat"

[![Latest Stable Version](https://poser.pugx.org/ctw/ctw-middleware-generatedat/v/stable)](https://packagist.org/packages/ctw/ctw-middleware-generatedat)
[![GitHub Actions](https://github.com/jonathanmaron/ctw-middleware-generatedat/actions/workflows/tests.yml/badge.svg)](https://github.com/jonathanmaron/ctw-middleware-generatedat/actions/workflows/tests.yml)
[![Scrutinizer Build](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedat/badges/build.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedat/build-status/master)
[![Scrutinizer Quality](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedat/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedat/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jonathanmaron/ctw-middleware-generatedat/?branch=master)

PSR-15 middleware that adds an `X-Generated-At` header with an ISO 8601 UTC timestamp to every response.

## Introduction

### Why This Library Exists

Knowing when a response was generated is valuable for debugging, monitoring, and cache validation. While HTTP provides standard caching headers like `Date` and `Last-Modified`, these are often set by web servers or proxies rather than the application itself.

The `X-Generated-At` header provides an application-level timestamp indicating exactly when your PHP code generated the response:

- **Debugging**: Quickly identify if you're seeing a cached response or a freshly generated one
- **Performance monitoring**: Compare generation timestamps with response receipt times
- **Cache verification**: Confirm that CDN or browser caches are serving expected content
- **Audit trails**: Log when specific responses were generated for compliance purposes

### Problems This Library Solves

1. **Cache ambiguity**: Difficult to determine if a response came from cache or was freshly generated
2. **Timezone confusion**: Server timestamps may use local time; this middleware uses consistent UTC format
3. **Missing application timestamps**: Web server `Date` headers don't reflect when application code executed
4. **Debugging complexity**: Without timestamps, correlating logs with responses requires additional tooling
5. **Inconsistent formatting**: Custom timestamp implementations vary; this provides ISO 8601 standard format

### Where to Use This Library

- **Development environments**: Verify that code changes are being reflected in responses
- **Production applications**: Monitor response freshness and debug caching issues
- **API services**: Provide clients with precise response generation timestamps
- **Multi-tier architectures**: Distinguish between application, CDN, and proxy caching
- **Debugging sessions**: Correlate browser responses with server-side logs

### Design Goals

1. **ISO 8601 format**: Uses standard `Y-m-d\TH:i:s\Z` format for universal parsing
2. **UTC timezone**: Eliminates timezone ambiguity with consistent UTC timestamps
3. **Request-based accuracy**: Uses `REQUEST_TIME_FLOAT` for precise timing
4. **Minimal overhead**: Simple header addition with negligible performance impact
5. **Non-intrusive**: Adds metadata without modifying response content

## Requirements

- PHP 8.3 or higher
- ctw/ctw-middleware ^4.0

## Installation

Install by adding the package as a [Composer](https://getcomposer.org) requirement:

```bash
composer require ctw/ctw-middleware-generatedat
```

## Usage Examples

### Basic Pipeline Registration (Mezzio)

```php
use Ctw\Middleware\GeneratedAtMiddleware\GeneratedAtMiddleware;

// In config/pipeline.php or similar
$app->pipe(GeneratedAtMiddleware::class);
```

### Response Header Output

```http
HTTP/1.1 200 OK
Content-Type: text/html; charset=UTF-8
X-Generated-At: 2024-01-15T14:30:45Z
```

### Inspecting with cURL

```bash
curl -I https://example.com/

# Response includes:
# X-Generated-At: 2024-01-15T14:30:45Z
```

### Inspecting in Browser DevTools

1. Open Developer Tools (F12)
2. Navigate to the Network tab
3. Select a request
4. View Response Headers
5. Look for `X-Generated-At`

### ConfigProvider Registration

The package includes a `ConfigProvider` for automatic factory registration:

```php
// config/config.php
return [
    // ...
    \Ctw\Middleware\GeneratedAtMiddleware\ConfigProvider::class,
];
```

### Header Format

| Component | Value | Description |
|-----------|-------|-------------|
| Header name | `X-Generated-At` | Custom header indicating generation time |
| Format | ISO 8601 | `Y-m-d\TH:i:s\Z` |
| Timezone | UTC | Indicated by `Z` suffix |
| Example | `2024-01-15T14:30:45Z` | January 15, 2024 at 14:30:45 UTC |
