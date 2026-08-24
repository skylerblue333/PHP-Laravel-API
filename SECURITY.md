# Security

## Supported status

This repository is an engineering-beta service. Security-sensitive production deployment is not yet verified.

## Current controls

- Laravel request validation for the exposed write example.
- Debug disabled by default in `.env.example` and the container runtime.
- Dependency audit in CI with `composer audit`.
- Non-root container runtime verification.
- No committed application secrets are required by the current API surface.

## Not yet provided

Authentication, authorization/RBAC, persistent audit logs, tenant isolation, TLS termination, rate limiting, WAF policy, production secret management, backup/restore, and deployment hardening are outside the verified scope of this checkpoint.

Report vulnerabilities privately to the repository owner rather than opening a public exploit report.
