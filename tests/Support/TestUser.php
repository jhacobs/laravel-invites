<?php

namespace Invites\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Invites\Contracts\CanBeInvited;

class TestUser extends Model implements CanBeInvited
{
    protected $guarded = [];

    protected $table = 'test_users';

    public $token = null;

    public function sendInviteNotification(string $token)
    {
        $this->token = $token;
    }

    public function getEmailForInvites(): string
    {
        return 'email';
    }
}
