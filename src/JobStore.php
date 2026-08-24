<?php

declare(strict_types=1);

namespace SkyPhpApi;

use InvalidArgumentException;
use RuntimeException;

final class JobStore
{
    public function __construct(private readonly string $path)
    {
    }

    /** @return array{status:string,job_id:string,queue_depth:int} */
    public function enqueue(array $payload): array
    {
        $jobId = trim((string)($payload['job_id'] ?? ''));
        $jobType = trim((string)($payload['job_type'] ?? ''));
        $params = $payload['params'] ?? null;
        $priority = $payload['priority'] ?? 5;

        if ($jobId === '' || strlen($jobId) > 128) {
            throw new InvalidArgumentException('job_id must be between 1 and 128 characters');
        }
        if ($jobType === '' || strlen($jobType) > 128) {
            throw new InvalidArgumentException('job_type must be between 1 and 128 characters');
        }
        if (!is_array($params)) {
            throw new InvalidArgumentException('params must be a JSON object');
        }
        if (!is_int($priority) || $priority < 1 || $priority > 10) {
            throw new InvalidArgumentException('priority must be an integer from 1 to 10');
        }

        $jobs = $this->read();
        foreach ($jobs as $job) {
            if (($job['job_id'] ?? null) === $jobId) {
                throw new InvalidArgumentException('job_id already exists');
            }
        }

        $jobs[] = [
            'job_id' => $jobId,
            'job_type' => $jobType,
            'params' => $params,
            'priority' => $priority,
            'created_at' => gmdate(DATE_ATOM),
        ];

        usort($jobs, static fn(array $a, array $b): int => [$a['priority'], $a['created_at'], $a['job_id']] <=> [$b['priority'], $b['created_at'], $b['job_id']]);
        $this->write($jobs);

        return ['status' => 'queued', 'job_id' => $jobId, 'queue_depth' => count($jobs)];
    }

    /** @return array<int,array<string,mixed>> */
    public function list(int $limit = 10): array
    {
        if ($limit < 1 || $limit > 100) {
            throw new InvalidArgumentException('limit must be between 1 and 100');
        }
        return array_slice($this->read(), 0, $limit);
    }

    public function count(): int
    {
        return count($this->read());
    }

    /** @return array<int,array<string,mixed>> */
    private function read(): array
    {
        if (!is_file($this->path)) {
            return [];
        }
        $raw = file_get_contents($this->path);
        if ($raw === false) {
            throw new RuntimeException('unable to read job store');
        }
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw new RuntimeException('invalid job store');
        }
        return $decoded;
    }

    /** @param array<int,array<string,mixed>> $jobs */
    private function write(array $jobs): void
    {
        $directory = dirname($this->path);
        if (!is_dir($directory) && !mkdir($directory, 0770, true) && !is_dir($directory)) {
            throw new RuntimeException('unable to create data directory');
        }
        $tmp = $this->path . '.tmp';
        $encoded = json_encode($jobs, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        if (file_put_contents($tmp, $encoded, LOCK_EX) === false || !rename($tmp, $this->path)) {
            @unlink($tmp);
            throw new RuntimeException('unable to persist job store');
        }
    }
}
