<?php

declare(strict_types=1);

namespace YourVendor\LaravelAutoValidator\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use YourVendor\LaravelAutoValidator\LaravelAutoValidatorServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [LaravelAutoValidatorServiceProvider::class];
    }
}
