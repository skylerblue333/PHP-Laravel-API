<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        foreach ([
            __DIR__.'/../storage/framework/cache',
            __DIR__.'/../storage/framework/sessions',
            __DIR__.'/../storage/framework/views',
            __DIR__.'/../bootstrap/cache',
        ] as $directory) {
            if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
                throw new \RuntimeException('failed to create Laravel runtime directory');
            }
        }

        /** @var Application $app */
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
}
