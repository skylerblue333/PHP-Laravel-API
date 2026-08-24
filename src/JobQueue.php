<?php

declare(strict_types=1);

namespace Skycoin4444\PhpApi;

use InvalidArgumentException;

final class JobQueue
{
    /** @var array<string, array{job_id:string, job_type:string, params:array<string,mixed>, priority:int, sequence:int}> */
    private array $jobs = [];

    private int $sequence = 0;

    /**
     * @param array<string,mixed> $params
     * @return array{job_id:string, job_type:string, params:array<string,mixed>, priority:int, sequence:int}
     */
    public function enqueue(string $jobId, string $jobType, array $params = [], int $priority = 5): array
    {
        $jobId = trim($jobId);
        $jobType = trim($jobType);

        if ($jobId === '' || strlen($jobId) > 100) {
            throw new InvalidArgumentException('job_id must contain 1-100 characters');
        }
        if ($jobType === '' || strlen($jobType) > 100) {
            throw new InvalidArgumentException('job_type must contain 1-100 characters');
        }
        if ($priority < 1 || $priority > 10) {
            throw new InvalidArgumentException('priority must be between 1 and 10');
        }
        if (isset($this->jobs[$jobId])) {
            throw new InvalidArgumentException('job_id already exists');
        }

        $job = [
            'job_id' => $jobId,
            'job_type' => $jobType,
            'params' => $params,
            'priority' => $priority,
            'sequence' => $this->sequence++,
        ];
        $this->jobs[$jobId] = $job;

        return $job;
    }

    /** @return list<array{job_id:string, job_type:string, params:array<string,mixed>, priority:int, sequence:int}> */
    public function list(int $limit = 10): array
    {
        if ($limit < 1 || $limit > 100) {
            throw new InvalidArgumentException('limit must be between 1 and 100');
        }

        $jobs = array_values($this->jobs);
        usort(
            $jobs,
            static fn (array $left, array $right): int =>
                [$left['priority'], $left['sequence']] <=> [$right['priority'], $right['sequence']]
        );

        return array_slice($jobs, 0, $limit);
    }

    public function count(): int
    {
        return count($this->jobs);
    }
}
