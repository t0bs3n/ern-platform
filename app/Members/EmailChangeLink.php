<?php

namespace App\Members;

use App\Models\User;
use Illuminate\Support\Facades\URL;

/** The link that confirms a new e-mail address (R1-12): signed, for 24 hours, tied to the pending address. */
final class EmailChangeLink
{
    public const HOURS = 24;

    public static function for(User $user): string
    {
        return URL::temporarySignedRoute('members.profile.email.confirm', now()->addHours(self::HOURS), [
            'user' => $user->id,
            'hash' => sha1((string) $user->pending_email),
        ]);
    }
}
