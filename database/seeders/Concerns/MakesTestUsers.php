<?php

namespace Database\Seeders\Concerns;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

trait MakesTestUsers
{
    private static ?string $hashedPassword = null;

    private static ?string $hashedFor = null;

    /** The local-only password of all test users, from SEED_PASSWORD, hashed once. */
    protected function testPassword(): string
    {
        $plain = $this->plainTestPassword();

        if (self::$hashedFor !== $plain) {
            self::$hashedPassword = Hash::make($plain);
            self::$hashedFor = $plain;
        }

        return self::$hashedPassword;
    }

    private function plainTestPassword(): string
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('The seeders make invented test data. They run only locally and in tests, never in production or on staging.');
        }

        $password = (string) config('ern.seed_password');

        if ($password === '') {
            throw new RuntimeException('Set SEED_PASSWORD in .env (see .env.example) before seeding.');
        }

        return $password;
    }

    /**
     * Create or update one invented, approved member with the member role and any extra roles.
     *
     * @param  array<string, mixed>  $details
     * @param  list<Role>  $roles
     */
    protected function testUser(array $details, array $roles = []): User
    {
        $user = User::where('email', Str::lower($details['email']))->first() ?? new User;

        $user->forceFill($details + [
            'slug' => Str::slug($details['first_name'].' '.$details['last_name']),
            'password' => $this->testPassword(),
            'approved_at' => now(),
            'email_verified_at' => now(),
        ])->save();

        foreach ([Role::Member, ...$roles] as $role) {
            $user->giveRole($role);
        }

        return $user;
    }
}
