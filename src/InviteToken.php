<?php

namespace Jhacobs\Invites;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;
use Jhacobs\Invites\Contracts\CanBeInvited;
use Jhacobs\Invites\Models\Invite;

class InviteToken
{
    protected $hasher;

    protected $hashKey;

    protected $expires;

    public function __construct(Hasher $hasher, string $hashKey, int $expires)
    {
        $this->hasher = $hasher;
        $this->hashKey = $hashKey;
        $this->expires = $expires;
    }

    public function create(CanBeInvited $user): string
    {
        $token = $this->createNewToken();

        $invite = new Invite();
        $invite->email = $user->getEmailForInvites();
        $invite->token = $this->hasher->make($token);
        $invite->created_at = now();
        $invite->save();

        return $token;
    }

    public function isValid(string $token, Invite $invite): bool
    {
        return $this->hasher->check($token, $invite->token) && ! $invite->isExpired();
    }

    protected function createNewToken(): string
    {
        return hash_hmac('sha256', Str::random(40), $this->hashKey);
    }
}
