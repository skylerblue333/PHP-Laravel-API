# Sky PHP Queue API

Engineering-beta standalone service in the SKYCOIN4444 portfolio.

> Repository-name note: `PHP-Laravel-API` is the historical repository name. The current verified implementation is a dependency-light PHP 8.3 HTTP service; it does **not** claim to be a full Laravel application.

## What it does

- `POST /api/v1/jobs` validates and enqueues bounded job requests.
- `GET /api/v1/jobs?limit=10` returns deterministic priority/FIFO ordering.
- `GET /healthz` provides a liveness endpoint.
- `GET /readyz` reports readiness and current in-memory queue depth.
- Duplicate IDs, empty identifiers/types, invalid priorities, and invalid limits are rejected.
- The runtime container executes as an unprivileged user.

## Run locally

```bash
php -S 127.0.0.1:8080 -t public
```

Then:

```bash
curl http://127.0.0.1:8080/healthz
curl -X POST http://127.0.0.1:8080/api/v1/jobs \
  -H 'content-type: application/json' \
  -d '{"job_id":"example-1","job_type":"index-feed","priority":2,"params":{"source":"community"}}'
```

## Verification

```bash
find src public tests -name '*.php' -print0 | xargs -0 -n1 php -l
php tests/JobQueueTest.php
docker build -t sky-php-api .
```

GitHub Actions runs syntax checks, deterministic queue tests, the container build, non-root verification, and an HTTP health smoke test.

## Product boundary

This checkpoint is an in-memory queue/API foundation. It does **not** claim durable persistence, distributed processing, authentication/authorization, Laravel framework parity, worker execution, HA, tenant isolation, managed deployment, or production SLA readiness. Those capabilities require separate implementation and verification.

## SKYCOIN4444 integration targets

The service can be adapted as a bounded internal job-ingress component for feed indexing, notifications, media processing, school workflows, marketplace tasks, or other ecosystem modules after authentication, persistence, observability, and deployment controls are added.

## License

See `LICENSE`.
