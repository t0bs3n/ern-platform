<?php

namespace Database\Factories;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Invented applicants only. Institutions carry "(example)".
 *
 * @extends Factory<MembershipApplication>
 */
class MembershipApplicationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'institution' => fake()->city().' University (example)',
            'country' => fake()->country(),
            'city' => fake()->city(),
            'career_stage' => fake()->randomElement(User::CAREER_STAGES),
            'interests' => 'Esports, research',
            'agreed_at' => now(),
            'status' => MembershipApplication::OPEN,
        ];
    }
}
