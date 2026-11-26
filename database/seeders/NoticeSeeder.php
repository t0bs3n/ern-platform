<?php

namespace Database\Seeders;

use App\Models\Notice;
use Illuminate\Database\Seeder;

/**
 * The example notices of the mockup (data.json `notices`). Their dates are examples,
 * so this runs only locally and in tests, through DatabaseSeeder.
 */
class NoticeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['General Assembly', 'General Assembly 2026', 'Wednesday, 16 December 2026, 14:00 (Oslo time), on Zoom. Motions are due by 9 December.'],
            ['Board election', 'Board election 2026', 'The call for nominations runs until 15 November. The election takes place at the General Assembly in December.'],
            ['Conference', 'ERNC27 in Raleigh', 'The Call for Abstracts is open until 4 January 2027. Decisions follow on 15 March, registration opens on 3 April 2027.'],
            ['Association', 'Statutes and minutes', 'The current statutes of ERN e.V. and the minutes of past General Assemblies.'],
        ] as $n => [$kind, $title, $body]) {
            if (Notice::where('title', $title)->exists()) {
                continue;
            }
            $notice = new Notice(['kind' => $kind, 'title' => $title, 'body' => $body, 'status' => Notice::PUBLISHED]);
            $notice->published_at = now()->subDays($n);
            $notice->save();
        }
    }
}
