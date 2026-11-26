<?php

namespace App\Members;

use App\Models\User;
use Illuminate\Support\Facades\URL;

/**
 * The link in the welcome e-mail (R1-10): signed, tied to the member, and valid for a few days.
 * R1-11 accepts it only while the member has no password yet, so it works once.
 */
final class ActivationLink
{
    public static function for(User $user): string
    {
        return URL::temporarySignedRoute(
            'members.activate',
            now()->addDays((int) config('ern.activation_days', 7)),
            ['user' => $user->id],
        );
    }
}
