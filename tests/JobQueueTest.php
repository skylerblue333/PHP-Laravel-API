<?php

declare(strict_types=1);

use Skycoin4444\PhpApi\JobQueue;

require __DIR__ . '/../src/JobQueue.php';

function assertSameValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        fwrite(STDERR, 'expected=' . var_export($expected, true) . ' actual=' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

$queue = new JobQueue();
$queue->enqueue('job-b', 'index', ['source' => 'feed'], 5);
$queue->enqueue('job-a', 'notify', [], 1);
$queue->enqueue('job-c', 'index', [], 5);

$jobs = $queue->list();
assertSameValue(3, $queue->count(), 'queue count mismatch');
assertSameValue('job-a', $jobs[0]['job_id'], 'priority ordering mismatch');
assertSameValue('job-b', $jobs[1]['job_id'], 'stable FIFO tie ordering mismatch');
assertSameValue('job-c', $jobs[2]['job_id'], 'stable FIFO tie ordering mismatch');
assertSameValue(2, count($queue->list(2)), 'limit mismatch');

$invalidPriorityRejected = false;
try {
    $queue->enqueue('bad-priority', 'test', [], 11);
} catch (InvalidArgumentException) {
    $invalidPriorityRejected = true;
}
assertSameValue(true, $invalidPriorityRejected, 'invalid priority was accepted');

$duplicateRejected = false;
try {
    $queue->enqueue('job-a', 'duplicate');
} catch (InvalidArgumentException) {
    $duplicateRejected = true;
}
assertSameValue(true, $duplicateRejected, 'duplicate job id was accepted');

echo "JobQueue tests passed\n";
