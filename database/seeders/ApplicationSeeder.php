<?php

namespace Database\Seeders;

use App\Models\MembershipApplication;
use Illuminate\Database\Seeder;

/**
 * The three invented applicants of the mockup (js/65-admin.js), waiting for the board.
 * Local and tests only, through DatabaseSeeder.
 */
class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['Noor', 'Haddad', 'Example University (example)', 'Jordan', 'PhD student', '2026-11-09'],
            ['Tomás', 'Herrera', 'Example Institute of Technology (example)', 'Chile', 'Researcher with a PhD', '2026-11-08'],
            ['Linh', 'Pham', 'Example Esports Club (example)', 'Vietnam', 'Practitioner in industry', '2026-11-06'],
        ] as [$first, $last, $institution, $country, $stage, $date]) {
            $email = strtolower(str_replace('á', 'a', $first.'.'.$last)).'@example.test';

            if (MembershipApplication::where('email', $email)->exists()) {
                continue;
            }

            $application = new MembershipApplication([
                'first_name' => $first, 'last_name' => $last, 'email' => $email,
                'institution' => $institution, 'country' => $country, 'career_stage' => $stage,
            ]);
            $application->forceFill([
                'agreed_at' => $date.' 10:00:00', 'status' => MembershipApplication::OPEN,
                'created_at' => $date.' 10:00:00', 'updated_at' => $date.' 10:00:00',
            ])->save();
        }
    }
}
