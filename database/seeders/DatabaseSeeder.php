<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Fills a local or test database with invented content: `php artisan migrate:fresh --seed`.
 * Runs only locally and in tests: never in production or on staging.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('The seeders make invented test data. They run only locally and in tests, never in production or on staging.');
        }

        $this->call([
            TestUserSeeder::class,
            MemberSeeder::class,
        ]);
    }
}
