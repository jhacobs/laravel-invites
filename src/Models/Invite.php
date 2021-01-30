<?php

namespace Jhacobs\Invites\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invite extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $guarded = [
        'email',
        'token',
        'created_at',
    ];

    protected $hidden = [
        'token',
    ];

    protected $dates = [
        'created_at',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(static function (self $invite) {
            $invite->{$invite->getKeyName()} = (string) Str::uuid();
        });
    }

    public function isExpired(): bool
    {
        $expiresIn = config('invites.tokens_expire_in');

        return $this->created_at->addMinutes($expiresIn)->isPast();
    }

    public function scopeExpired(Builder $query): Builder
    {
        $expiresIn = config('invites.tokens_expire_in');

        $date = now()->subMinutes($expiresIn)->endOfDay();

        return $query->whereDate('created_at', '<', $date);
    }
}
