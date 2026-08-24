# Security Policy

## Status

Sky PHP Queue API is an **engineering beta**. CI checks PHP syntax, deterministic domain tests, Laravel feature tests, Composer advisories, container construction, non-root execution, and an HTTP health smoke test. Production infrastructure is not verified by this repository.

## Implemented controls

- Laravel request validation bounds identifiers, types, priorities, list limits, and parameter shape.
- The queue rejects duplicate job IDs and invalid domain values independently of the HTTP layer.
- The runtime container uses an unprivileged user.
- Runtime dependencies are checked with `composer audit` in CI.
- No credentials or application key are committed; deployments must inject `APP_KEY` and other secrets.

## Boundaries

The service does not implement authentication, authorization, tenant isolation, durable storage, distributed locking, worker execution, retries/dead-letter queues, TLS termination, rate limiting, cryptographic job signing, or durable audit history. Queue state is process-local and ephemeral.

Place the service behind authenticated network controls and add a durable queue/persistence adapter before using it for consequential workloads.

## Reporting

Use GitHub private vulnerability reporting when available. Do not publish credentials, private job payloads, or working exploit details in public issues.
