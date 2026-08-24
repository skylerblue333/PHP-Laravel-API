# Sky Laravel API

**Status: engineering beta.** This repository is a focused PHP 8.3 / Laravel 12 HTTP API productization checkpoint. The legacy Python placeholder remains in Git history and is no longer the active runtime on this branch.

## What it provides

- `GET /api/health` — liveness response.
- `GET /api/ready` — lightweight application readiness response.
- `POST /api/echo` — validated JSON request example with bounded input and generated request object ID.
- Laravel validation and JSON-aware exception handling.
- PHPUnit/Testbench feature coverage for health and validation behavior.
- Composer validation/audit, Laravel Pint, route boot verification, Docker build, and non-root image verification in CI.

## Local verification

Requirements: PHP 8.3+ and Composer 2.

```bash
cp .env.example .env
composer install
php artisan route:list
composer test
vendor/bin/pint --test
composer audit --locked
php artisan serve --host=127.0.0.1 --port=8080
```

Then call `http://127.0.0.1:8080/api/health`.

## Container

```bash
docker build -t sky-laravel-api .
docker run --rm -p 8080:8080 sky-laravel-api
```

The runtime image uses an unprivileged application user. Environment defaults intentionally disable debug output.

## Architecture

`public/index.php` is the HTTP entrypoint. `bootstrap/app.php` configures Laravel and loads `routes/api.php`. The current product deliberately avoids database state, queues, authentication, external services, and hidden infrastructure dependencies so its verified surface stays small and truthful.

## SKYCOIN4444 integration

This service can serve as a reusable Laravel-side adapter or bounded API example for the wider ecosystem. Integration should happen through documented HTTP contracts rather than copying the implementation into another repository.

## Boundaries

This checkpoint does **not** claim production deployment, persistent storage, tenant isolation, RBAC, HA, queue processing, rate limiting, TLS termination, or a complete business API. Those require separate implementation and runtime evidence before being advertised.

See `SECURITY.md` for security boundaries and `CHANGELOG.md` for productization history.
