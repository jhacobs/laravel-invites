<?php

namespace Jhacobs\Invites;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class InviteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/invites.php' => config_path('invites.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/invites.php', 'invites');

        $this->app->singleton(InviteManager::class, function ($app) {
            return new InviteManager(
                $this->createInviteToken(),
                $app['auth']->createUserProvider(config('invites.user_provider'))
            );
        });

        $this->app->singleton('invite', function ($app) {
            return $app->make(InviteManager::class);
        });
    }

    protected function createInviteToken(): InviteToken
    {
        $key = $this->app['config']['app.key'];

        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return new InviteToken($this->app['hash'], $key, config('invites.tokens_expire_in'));
    }
}
