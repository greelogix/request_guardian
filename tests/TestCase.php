<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Greelogix\RequestGuardian\LaravelAutoValidatorServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [LaravelAutoValidatorServiceProvider::class];
    }
}
