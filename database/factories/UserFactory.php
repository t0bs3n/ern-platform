<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Invented members only. Institutions always carry "(example)".
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $first = fake()->firstName();
        $last = fake()->lastName();

        return [
            'first_name' => $first,
            'last_name' => $last,
            'slug' => Str::slug($first.' '.$last).'-'.Str::lower(Str::random(6)),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'institution' => fake()->city().' University (example)',
            'country' => fake()->country(),
            'career_stage' => fake()->randomElement(User::CAREER_STAGES),
            'listed_in_directory' => false,
            'approved_at' => now(),
        ];
    }

    /** Every user made by the factory is a member. */
    public function configure(): static
    {
        return $this->afterCreating(fn (User $user) => $user->giveRole(Role::Member));
    }

    /** Extra roles on top of member. */
    public function withRoles(Role ...$roles): static
    {
        return $this->afterCreating(function (User $user) use ($roles) {
            foreach ($roles as $role) {
                $user->giveRole($role);
            }
        });
    }

    /** An applicant who has not been approved yet. */
    public function unapproved(): static
    {
        return $this->state(fn () => ['approved_at' => null]);
    }

    /** A member who has not set a password yet. */
    public function withoutPassword(): static
    {
        return $this->state(fn () => ['password' => null]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
