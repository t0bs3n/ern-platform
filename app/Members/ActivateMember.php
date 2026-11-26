<?php

namespace App\Members;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Sets a new member's first password (R1-11). Returns null if the member has a password
 * already or is no longer approved, so that a link works only once.
 */
final class ActivateMember
{
    public function __invoke(int $userId, string $password): ?User
    {
        return DB::transaction(function () use ($userId, $password) {
            $user = User::whereKey($userId)->lockForUpdate()->first();

            if ($user === null || ! $user->isApproved() || $user->password !== null) {
                return null;
            }

            // The model's "hashed" cast stores the password hashed. Never update it through the query builder.
            $user->forceFill(['password' => $password, 'email_verified_at' => now()])->save();

            return $user;
        });
    }
}
