<?php

namespace App\Models;

use App\Enums\Role;
use App\Mail\PasswordResetLink;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

// approved_at is deliberately not fillable: only the approval (R1-10) and the import (R1-19) set it.
#[Fillable([
    'first_name', 'last_name', 'slug', 'email', 'password', 'institution', 'country',
    'career_stage', 'interests', 'orcid', 'discord_name', 'listed_in_directory',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Career stages, as on the application form. */
    public const CAREER_STAGES = [
        'PhD student',
        'Researcher with a PhD',
        'Professor',
        'Student',
        'Practitioner in industry',
        'Other',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'listed_in_directory' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /** E-mail addresses are stored in lower case, so look-ups match on SQLite and MySQL alike. */
    protected function email(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => $value === null ? null : Str::lower(trim($value)));
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(get: fn () => trim($this->first_name.' '.$this->last_name));
    }

    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    /**
     * @return HasMany<UserRole, $this>
     */
    public function roles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * True if the user holds at least one of the roles. A user who is not
     * approved (or no longer approved) holds no rights at all.
     */
    public function hasRole(Role ...$roles): bool
    {
        if (! $this->isApproved()) {
            return false;
        }

        return $this->roles->contains(fn (UserRole $held) => in_array($held->role, $roles, true));
    }

    /** Give the user a role. Does nothing if they hold it already. */
    public function giveRole(Role $role): void
    {
        $this->roles()->firstOrCreate(['role' => $role]);
        $this->unsetRelation('roles');
    }

    /**
     * Our own e-mail with ERN wording, in place of the framework's notification.
     * Only approved members get it. The person asking sees the same answer either way.
     */
    public function sendPasswordResetNotification($token): void
    {
        if (! $this->isApproved()) {
            return;
        }

        Mail::to($this->email)->send(new PasswordResetLink($this, $token));
    }
}
