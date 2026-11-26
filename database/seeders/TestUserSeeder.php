<?php

namespace Database\Seeders;

use App\Enums\Role;
use Database\Seeders\Concerns\MakesTestUsers;
use Illuminate\Database\Seeder;

/**
 * One invented test user per role, with an obvious name and the address <role>@example.test.
 */
class TestUserSeeder extends Seeder
{
    use MakesTestUsers;

    public function run(): void
    {
        foreach (Role::cases() as $role) {
            $this->testUser([
                'first_name' => 'Test',
                'last_name' => $role->label(),
                'slug' => 'test-'.str_replace('_', '-', $role->value),
                'email' => $role->value.'@example.test',
                'institution' => 'ERN Test Institute (example)',
                'country' => 'Germany',
                'career_stage' => 'Other',
                'listed_in_directory' => false,
            ], $role === Role::Member ? [] : [$role]);
        }
    }
}
