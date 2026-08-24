<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class JobApiTest extends TestCase
{
    public function test_health_and_readiness_are_available(): void
    {
        $this->getJson('/healthz')->assertOk();
        $this->getJson('/api/v1/readyz')
            ->assertOk()
            ->assertJsonPath('status', 'ready')
            ->assertJsonPath('jobs', 0);
    }

    public function test_jobs_are_validated_and_sorted_by_priority(): void
    {
        $this->postJson('/api/v1/jobs', [
            'job_id' => 'later',
            'job_type' => 'report',
            'priority' => 8,
            'params' => ['format' => 'json'],
        ])->assertCreated();

        $this->postJson('/api/v1/jobs', [
            'job_id' => 'first',
            'job_type' => 'sync',
            'priority' => 2,
        ])->assertCreated();

        $this->getJson('/api/v1/jobs?limit=2')
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonPath('jobs.0.job_id', 'first')
            ->assertJsonPath('jobs.1.job_id', 'later');
    }

    public function test_invalid_and_duplicate_jobs_fail_closed(): void
    {
        $this->postJson('/api/v1/jobs', [
            'job_id' => '',
            'job_type' => 'sync',
            'priority' => 5,
        ])->assertUnprocessable();

        $payload = [
            'job_id' => 'unique-id',
            'job_type' => 'sync',
            'priority' => 5,
        ];

        $this->postJson('/api/v1/jobs', $payload)->assertCreated();
        $this->postJson('/api/v1/jobs', $payload)
            ->assertUnprocessable()
            ->assertJsonPath('error', 'job_id already exists');
    }
}
