<?php

namespace Invites\Contracts;

interface CanBeInvited
{
    public function sendInviteNotification(string $token);

    public function getEmailForInvites(): string;
}
