<?php

declare(strict_types=1);

use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;
use Skycoin4444\PhpApi\JobQueue;

Route::get('/v1/readyz', static function (JobQueue $queue): array {
    return [
        'status' => 'ready',
        'service' => 'sky-php-api',
        'jobs' => $queue->count(),
    ];
});

Route::get('/v1/jobs', [JobController::class, 'index']);
Route::post('/v1/jobs', [JobController::class, 'store']);
