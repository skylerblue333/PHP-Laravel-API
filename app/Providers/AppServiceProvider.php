<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Skycoin4444\PhpApi\JobQueue;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(JobQueue::class, static fn (): JobQueue => new JobQueue());
    }
}
