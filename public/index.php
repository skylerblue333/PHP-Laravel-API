<?php

declare(strict_types=1);

use Skycoin4444\PhpApi\JobQueue;

require __DIR__ . '/../src/JobQueue.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

/** @var JobQueue $queue */
$queue = $GLOBALS['sky_job_queue'] ??= new JobQueue();

function respond(int $status, array $payload): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    exit;
}

if ($method === 'GET' && $path === '/healthz') {
    respond(200, ['status' => 'ok', 'service' => 'sky-php-api']);
}

if ($method === 'GET' && $path === '/readyz') {
    respond(200, ['status' => 'ready', 'jobs' => $queue->count()]);
}

if ($method === 'GET' && $path === '/api/v1/jobs') {
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
    try {
        respond(200, ['jobs' => $queue->list($limit), 'total' => $queue->count()]);
    } catch (InvalidArgumentException $error) {
        respond(422, ['error' => $error->getMessage()]);
    }
}

if ($method === 'POST' && $path === '/api/v1/jobs') {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw === false ? '' : $raw, true);
    if (!is_array($body)) {
        respond(400, ['error' => 'request body must be a JSON object']);
    }

    try {
        $job = $queue->enqueue(
            (string) ($body['job_id'] ?? ''),
            (string) ($body['job_type'] ?? ''),
            is_array($body['params'] ?? null) ? $body['params'] : [],
            isset($body['priority']) ? (int) $body['priority'] : 5,
        );
        respond(201, ['status' => 'queued', 'job' => $job, 'queue_depth' => $queue->count()]);
    } catch (InvalidArgumentException $error) {
        respond(422, ['error' => $error->getMessage()]);
    }
}

respond(404, ['error' => 'route not found']);
