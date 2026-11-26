<?php

namespace App\Models;

use Database\Factories\MembershipApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * An application for membership (R1-09). It holds: name, e-mail address, institution,
 * country, city, career stage, research interests, Discord name and the time of the
 * agreement to the Code of Conduct and the privacy policy. No IP address, no browser data.
 * status and agreed_at are set by the server only.
 */
#[Fillable(['first_name', 'last_name', 'email', 'institution', 'country', 'city', 'career_stage', 'interests', 'discord_name'])]
class MembershipApplication extends Model
{
    /** @use HasFactory<MembershipApplicationFactory> */
    use HasFactory;

    public const OPEN = 'open';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['agreed_at' => 'datetime'];
    }

    protected function email(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => $value === null ? null : Str::lower(trim($value)));
    }

    /** True if the address already belongs to a user or to an open application. */
    public static function addressIsTaken(string $email): bool
    {
        $email = Str::lower(trim($email));

        return User::where('email', $email)->exists()
            || static::where('email', $email)->where('status', self::OPEN)->exists();
    }
}
