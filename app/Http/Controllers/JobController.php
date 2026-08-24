<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Skycoin4444\PhpApi\JobQueue;

final class JobController
{
    public function index(Request $request, JobQueue $queue): JsonResponse
    {
        $validated = $request->validate([
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($validated['limit'] ?? 10);

        return response()->json([
            'jobs' => $queue->list($limit),
            'total' => $queue->count(),
        ]);
    }

    public function store(Request $request, JobQueue $queue): JsonResponse
    {
        $validated = $request->validate([
            'job_id' => ['required', 'string', 'max:100'],
            'job_type' => ['required', 'string', 'max:100'],
            'params' => ['sometimes', 'array'],
            'priority' => ['sometimes', 'integer', 'between:1,10'],
        ]);

        try {
            $job = $queue->enqueue(
                $validated['job_id'],
                $validated['job_type'],
                $validated['params'] ?? [],
                (int) ($validated['priority'] ?? 5),
            );
        } catch (InvalidArgumentException $error) {
            return response()->json(['error' => $error->getMessage()], 422);
        }

        return response()->json([
            'status' => 'queued',
            'job' => $job,
            'queue_depth' => $queue->count(),
        ], 201);
    }
}
