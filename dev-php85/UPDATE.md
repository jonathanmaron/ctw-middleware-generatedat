# PHP 8.5.7 Migration — `ctw/ctw-middleware-generatedat`

- **Branch:** `php85` (cut from `master`)
- **Runtime:** PHP 8.3.31 → **8.5.7**
- **PHPUnit:** 12 → **13.2.1**
- **Date:** 2026-06-25
- **Status:** ✅ done

This is the completed migration checklist for running this package cleanly under
PHP 8.5.7. Every box is checked and verified against a fresh audit (see
**Final audit** below).

---

## Audit checklist

### `composer.json` — dependency resolution

- [x] **(fatal) `composer update -W` aborts** — `laminas/laminas-diactoros`
  2.x caps PHP at `~8.3.0`, so the solver rejects PHP 8.5.7.

  ```
  Problem 1
    - Root composer.json requires ctw/ctw-middleware ^4.0
    - ctw/ctw-middleware[4.0.0 ... 4.0.6] require laminas/laminas-diactoros ^2.11
    - laminas/laminas-diactoros[2.11 ... 2.26] require php ~8.0 || ~8.1 || ~8.2 || ~8.3
      -> your php version (8.5.7) does not satisfy that requirement.
  ```

  This package has **no direct `laminas-diactoros` dependency**; the cap is
  pulled transitively through `ctw/ctw-middleware ^4.0`.
  **Fix:** require `ctw/ctw-middleware: dev-php85`, which bumps diactoros → `^3`
  (installs 3.8.0) and `middlewares/utils` → `^4` (installs 4.0.2). No direct
  constraint change is needed in this package.

### Vendor runtime deprecations (`middlewares/utils`)

All five "implicitly nullable parameter" deprecations originate in the
third-party `middlewares/utils` dependency — **no first-party `src/` change is
required.**

- [x] **(deprecation) `vendor/middlewares/utils/src/Dispatcher.php:21`** —
  `Dispatcher::run()` `$request` implicitly nullable.
  **Fix:** cleared by the `middlewares/utils` → `^4` bump (v4 declares explicit
  `?type` parameters); pulled in via `ctw/ctw-middleware: dev-php85`.
- [x] **(deprecation) `vendor/middlewares/utils/src/Factory.php:88`** —
  `Factory::createUploadedFile()` `$size` implicitly nullable.
  **Fix:** cleared by the `middlewares/utils` → `^4` bump.
- [x] **(deprecation) `vendor/middlewares/utils/src/Factory.php:90`** —
  `Factory::createUploadedFile()` `$filename` implicitly nullable.
  **Fix:** cleared by the `middlewares/utils` → `^4` bump.
- [x] **(deprecation) `vendor/middlewares/utils/src/Factory.php:91`** —
  `Factory::createUploadedFile()` `$mediaType` implicitly nullable.
  **Fix:** cleared by the `middlewares/utils` → `^4` bump.
- [x] **(deprecation) `vendor/middlewares/utils/src/CallableHandler.php:25`** —
  `CallableHandler::__construct()` `$responseFactory` implicitly nullable.
  **Fix:** cleared by the `middlewares/utils` → `^4` bump.

### PHPUnit 13 — test-double migration

PHPUnit was bumped `^12` → `^13`. The lone expectation-free `ContainerInterface`
double built with `createMock()` emits a "mock object without expectations"
notice under PHPUnit 13.

- [x] **(notice) `test/GeneratedAtMiddlewareFactoryTest.php:52`** — the PSR-11
  `ContainerInterface` double in `testFactoryWorksWithPsr11Container()` carries
  no expectations.
  **Fix:** migrate to `createStub()`.
- [x] **(tooling) `test/GeneratedAtMiddlewareFactoryTest.php:52`** — PHPStan
  `staticMethod.dynamicCall`: `createStub()` is a `static` method in PHPUnit 13,
  so `$this->createStub()` is a dynamic call to a static method. (This package's
  `phpstan.neon` analyzes `src` **and** `test`, so the issue is in scope.)
  **Fix:** call it statically — `self::createStub(ContainerInterface::class)`.

---

## composer.json & CI

- [x] **require `php`** — `^8.3` → **`^8.5`**. Drops PHP 8.3/8.4 from the
  supported range.
- [x] **`ctw/ctw-middleware`** — `^4.0` → **`dev-php85`**. Transitively bumps
  diactoros → 3.8.0 and `middlewares/utils` → 4.0.2 (the diactoros blocker fix).
- [x] **`ctw/ctw-qa`** (dev) — `^5.0` → **`dev-php85`**. PHP 8.5-compatible QA
  toolchain.
- [x] **`phpunit/phpunit`** (dev) — `^12.0` → **`^13.0`** (installs 13.2.1).
- [x] **`.github/workflows/tests.yml`** — matrix pinned to **PHP 8.5 only**
  (`php: [ '8.5' ]`).
- [x] **Tests** — the expectation-free `ContainerInterface` double migrated to
  `self::createStub()`.

> Before merge: re-tag `ctw/ctw-middleware` (and `ctw/ctw-qa`) to stable
> releases and replace the `dev-php85` pins.

---

## Final audit (PHP 8.5.7)

- [x] **`php -v`** → PHP **8.5.7** (cli).
- [x] **`composer update -W`** → clean; no security advisories. Resolves
  `laminas/laminas-diactoros 3.8.0`, `middlewares/utils 4.0.2`,
  `ctw/ctw-middleware dev-php85`, `phpunit/phpunit 13.2.1`.
- [x] **PHPUnit** (`--no-coverage --display-deprecations --display-warnings
  --display-notices --display-errors`) → **OK (33 tests, 46 assertions)**, 0
  deprecations / warnings / notices.
- [x] **PHPStan** → no issues found (analyzes `src` and `test`).
