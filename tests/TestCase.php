<?php

namespace Williamug\Versioning\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Williamug\Versioning\VersioningServiceProvider;

class TestCase extends Orchestra
{
    public static $latestResponse;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Williamug\\Versioning\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            VersioningServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
            $migration = include __DIR__.'/../database/migrations/create_versioning_table.php.stub';
            $migration->up();
            */
    }
}
