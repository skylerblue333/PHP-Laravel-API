<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'service' => 'sky-laravel-api',
]));

Route::get('/ready', fn () => response()->json([
    'status' => 'ready',
    'checks' => ['runtime' => 'ok'],
]));

Route::post('/echo', function (Request $request) {
    $validated = $request->validate([
        'message' => ['required', 'string', 'min:1', 'max:500'],
    ]);

    return response()->json([
        'id' => (string) Str::uuid(),
        'message' => trim($validated['message']),
    ], 201);
});
