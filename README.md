# Sky PHP Queue API — Laravel

**Status: engineering beta.** This repository now contains an actual Laravel 12 API on PHP 8.3, backed by a small in-memory queue domain. CI verifies Composer resolution/audit, PHP syntax, domain and Laravel feature tests, container build, non-root execution, and an HTTP health smoke test. Durable deployment is not verified here.

## Implemented API

- `GET /healthz` — Laravel liveness endpoint.
- `GET /api/v1/readyz` — readiness plus current in-memory job count.
- `POST /api/v1/jobs` — enqueue a validated job.
- `GET /api/v1/jobs?limit=10` — list jobs in deterministic priority/FIFO order.

A job contains a unique `job_id` (1–100 chars), `job_type` (1–100 chars), optional object-like `params`, and priority 1–10. Lower priority values are returned first; equal priorities retain insertion order. Duplicate IDs and invalid bounds fail closed with client errors.

## Local verification

```bash
composer update --no-interaction --prefer-dist
find app bootstrap public routes src tests -name '*.php' -print0 | xargs -0 -n1 php -l
php tests/JobQueueTest.php
vendor/bin/phpunit --testdox
composer audit --locked
```

Run locally:

```bash
cp .env.example .env
php artisan key:generate
php -S 127.0.0.1:8080 -t public public/index.php
```

Example:

```bash
curl http://127.0.0.1:8080/healthz
curl -X POST http://127.0.0.1:8080/api/v1/jobs \
  -H 'accept: application/json' \
  -H 'content-type: application/json' \
  -d '{"job_id":"example-1","job_type":"index-feed","priority":2,"params":{"source":"community"}}'
```

## Container

```bash
docker build -t sky-php-api .
docker run --rm -p 8080:8080 -e APP_KEY='base64:REPLACE_WITH_A_REAL_KEY' sky-php-api
```

The runtime image uses an unprivileged UID rather than root. Supply a generated Laravel `APP_KEY` in deployed environments; do not commit it.

## Architecture

Laravel owns HTTP routing, request validation, JSON responses, application bootstrap, and dependency injection. `src/JobQueue.php` remains a framework-independent queue domain with bounded identifiers, duplicate rejection, priority validation, stable ordering, and deterministic unit tests. `AppServiceProvider` registers one queue instance for the process lifetime.

This separation keeps the domain reusable while making the repository truthfully match its Laravel name.

## SKYCOIN4444 integration

Use the versioned HTTP API as an independently deployable internal boundary for bounded job ingress such as feed indexing, notifications, media processing, school workflows, or marketplace tasks. Authentication, durable queueing, workers, retries/dead-letter handling, persistence, authorization, tenant isolation, metrics export, and distributed coordination belong in explicit future adapters rather than being implied here.

## Limits

This beta is **not** a durable job broker, worker platform, multi-node queue, HA deployment, or production SLA. Process restarts lose queued jobs. Deployment security and infrastructure validation remain pending.

See `SECURITY.md` and `CHANGELOG.md`.
