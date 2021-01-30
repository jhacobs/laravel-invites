<?php

namespace Jhacobs\Invites\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Jhacobs\Invites\InviteManager;
use Jhacobs\Invites\Models\Invite;
use Jhacobs\Invites\Tests\Support\TestUser;

class InviteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_send_an_invite_link(): void
    {
        /** @var InviteManager $inviteManager */
        $inviteManager = resolve('invite');

        /** @var TestUser $user */
        $user = TestUser::create([
            'email' => 'test@example.com',
            'password' => null,
        ]);

        $status = $inviteManager->sendInviteLink($user);

        self::assertSame(InviteManager::INVITE_LINK_SEND, $status);
        self::assertNotNull($user->token);
    }

    /** @test */
    public function it_can_create_a_user_password(): void
    {
        /** @var InviteManager $inviteManager */
        $inviteManager = resolve('invite');

        /** @var TestUser $user */
        $user = TestUser::create([
            'email' => 'test@example.com',
            'password' => null,
        ]);

        Invite::forceCreate([
            'email' => 'test@example.com',
            'token' => Hash::make('123456789'),
            'created_at' => now(),
        ]);

        $userPassword = null;
        $inviteUser = null;

        $status = $inviteManager->createPassword('test@example.com', '123456789', 'password1', function (TestUser $user, string $password) use (&$userPassword, &$inviteUser) {
            $userPassword = $password;
            $inviteUser = $user;
        });

        $user->refresh();

        self::assertSame('password1', $userPassword);
        self::assertSame($user->id, $inviteUser->id);
    }

    /** @test */
    public function it_cannot_create_a_user_password_with_an_invalid_user(): void
    {
        /** @var InviteManager $inviteManager */
        $inviteManager = resolve('invite');

        /** @var TestUser $user */
        $user = TestUser::create([
            'email' => 'test@example.com',
            'password' => null,
        ]);

        Invite::forceCreate([
            'email' => 'test@example.com',
            'token' => Hash::make('123456789'),
            'created_at' => now(),
        ]);

        $userPassword = null;
        $inviteUser = null;

        $status = $inviteManager->createPassword('invalid@example.com', '123456789', 'password1', function (TestUser $user, string $password) use (&$userPassword, &$inviteUser) {
            $userPassword = $password;
            $inviteUser = $user;
        });

        $user->refresh();

        self::assertNull($userPassword);
        self::assertSame(InviteManager::USER_NOT_FOUND, $status);
    }

    /** @test */
    public function it_cannot_create_a_user_password_with_an_invalid_token(): void
    {
        /** @var InviteManager $inviteManager */
        $inviteManager = resolve('invite');

        /** @var TestUser $user */
        $user = TestUser::create([
            'email' => 'test@example.com',
            'password' => null,
        ]);

        Invite::forceCreate([
            'email' => 'test@example.com',
            'token' => Hash::make('123456789'),
            'created_at' => now(),
        ]);

        $userPassword = null;
        $inviteUser = null;

        $status = $inviteManager->createPassword('test@example.com', 'invalid', 'password1', function (TestUser $user, string $password) use (&$userPassword, &$inviteUser) {
            $userPassword = $password;
            $inviteUser = $user;
        });

        $user->refresh();

        self::assertNull($userPassword);
        self::assertSame(InviteManager::INVALID_TOKEN, $status);
    }

    /** @test */
    public function it_cannot_create_a_user_password_with_an_expired_token(): void
    {
        /** @var InviteManager $inviteManager */
        $inviteManager = resolve('invite');

        /** @var TestUser $user */
        $user = TestUser::create([
            'email' => 'test@example.com',
            'password' => null,
        ]);

        Invite::forceCreate([
            'email' => 'test@example.com',
            'token' => Hash::make('123456789'),
            'created_at' => now()->subWeek(),
        ]);

        $userPassword = null;
        $inviteUser = null;

        $status = $inviteManager->createPassword('test@example.com', '123456789', 'password1', function (TestUser $user, string $password) use (&$userPassword, &$inviteUser) {
            $userPassword = $password;
            $inviteUser = $user;
        });

        $user->refresh();

        self::assertNull($userPassword);
        self::assertSame(InviteManager::INVALID_TOKEN, $status);
    }
}
