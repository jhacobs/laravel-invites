<?php

namespace Jhacobs\Invites\Tests;

use Jhacobs\Invites\Facades\Invite;
use Jhacobs\Invites\InviteServiceProvider;
use Jhacobs\Invites\Tests\Support\TestUser;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadLaravelMigrations();
    }

    protected function getPackageProviders($app)
    {
        return [InviteServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Invite' => Invite::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('app.key', 'base64:8u7/UMz3aTLZPVv+PXODqFlcc/1rMNtQeFLOpDK7exs=');

        $app['config']->set('auth.providers.users.model', TestUser::class);
    }

    protected function useSqliteConnection($app)
    {
        $app->config->set('database.default', 'sqlite');
    }
}
