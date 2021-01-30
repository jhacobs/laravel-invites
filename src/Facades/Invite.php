<?php

namespace Jhacobs\Invites\Facades;

use Illuminate\Support\Facades\Facade;
use Jhacobs\Invites\Contracts\CanBeInvited;

/**
 * @see \Invites\InviteManager
 *
 * @method static string sendInviteLink(CanBeInvited $invitable)
 * @method static string createPassword(string $email, string $token, string $password, callable $callback)
 */
class Invite extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'invite';
    }
}
