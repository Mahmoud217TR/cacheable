<?php

namespace Mahmoud217TR\Cacheable\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Mahmoud217TR\Cacheable\CacheableServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            CacheableServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('cache.default', 'array');
    }
}
