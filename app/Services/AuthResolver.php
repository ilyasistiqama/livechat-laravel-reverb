<?php

namespace App\Services;

use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthResolver
{
    public static function resolve(): ?object
    {
        if (Auth::guard('admin')->check()) {
            return (object) [
                'guard' => 'admin',
                'type'  => 'admin',
                'user'  => Auth::guard('admin')->user(),
            ];
        }

        if (Auth::guard('member')->check()) {
            return (object) [
                'guard' => 'member',
                'type'  => 'member',
                'user'  => Auth::guard('member')->user(),
            ];
        }

        return null;
    }


    public static function resolveByUserId(int $userId): ?object
    {
        if ($admin = User::find($userId)) {
            return (object) [
                'guard' => 'admin',
                'type'  => 'admin',
                'user'  => $admin,
            ];
        }

        if ($member = Member::find($userId)) {
            return (object) [
                'guard' => 'member',
                'type'  => 'member',
                'user'  => $member,
            ];
        }

        return null;
    }

    public static function user()
    {
        return self::resolve()?->user;
    }

    public static function isAdmin(): bool
    {
        return self::resolve()?->guard === 'admin';
    }

    public static function isMember(): bool
    {
        return self::resolve()?->guard === 'member';
    }
}
