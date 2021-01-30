<?php

namespace Invites;

use Illuminate\Contracts\Auth\UserProvider;
use Invites\Contracts\CanBeInvited;
use Invites\Models\Invite;

class InviteManager
{
    public const PASSWORD_CREATED = 'password.created';

    public const USER_NOT_FOUND = 'invite.user';

    public const INVALID_TOKEN = 'invite.token';

    public const INVITE_LINK_SEND = 'invite.send';

    protected $tokens;

    protected $users;

    public function __construct(InviteToken $tokens, UserProvider $users)
    {
        $this->tokens = $tokens;
        $this->users = $users;
    }

    public function sendInviteLink(CanBeInvited $invitable): string
    {
        $token = $this->tokens->create($invitable);

        $invitable->sendInviteNotification($token);

        return static::INVITE_LINK_SEND;
    }

    public function createPassword(string $email, string $token, string $password, callable $callback): string
    {
        $user = $this->users->retrieveByCredentials([
            'email' => $email,
        ]);

        if (! $user) {
            return static::USER_NOT_FOUND;
        }

        $invite = $this->getInvite($email);

        if (! $this->isInviteValid($token, $invite)) {
            return static::INVALID_TOKEN;
        }

        $callback($user, $password);

        $invite->delete();

        return static::PASSWORD_CREATED;
    }

    protected function getInvite(string $email): Invite
    {
        return Invite::firstWhere('email', $email);
    }

    protected function isInviteValid(string $token, Invite $invite): bool
    {
        return $invite !== null && $this->tokens->isValid($token, $invite);
    }
}
