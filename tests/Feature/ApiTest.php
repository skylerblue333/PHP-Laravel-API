<?php

declare(strict_types=1);

namespace Tests\Feature;

use Orchestra\Testbench\TestCase;

final class ApiTest extends TestCase
{
    protected function defineRoutes($router): void
    {
        $router->prefix('api')->group(function (): void {
            require __DIR__.'/../../routes/api.php';
        });
    }

    public function test_health_is_available(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJson(['status' => 'ok', 'service' => 'sky-laravel-api']);
    }

    public function test_echo_validates_and_returns_message(): void
    {
        $this->postJson('/api/echo', ['message' => ' hello '])
            ->assertCreated()
            ->assertJson(['message' => 'hello'])
            ->assertJsonStructure(['id', 'message']);

        $this->postJson('/api/echo', ['message' => ''])
            ->assertUnprocessable();
    }
}
