<?php

namespace Database\Seeders;

use App\Models\Event;
use Carbon\CarbonImmutable;
use Database\Seeders\Concerns\LocalOnly;
use Illuminate\Database\Seeder;

/**
 * Example events of the association, as in the mockup (js/45-events.js). The dates are
 * the mockup's examples, so this seeder runs only through DatabaseSeeder (local and tests).
 */
class EventSeeder extends Seeder
{
    use LocalOnly;

    public function run(): void
    {
        $this->ensureLocal();

        $zone = 'Europe/Oslo';
        $events = [
            ['Call for nominations to the board closes', 'Association', '2026-11-15', null, null, 'ERN members', '/members'],
            ['Motions for the General Assembly are due', 'Association', '2026-12-09', null, null, 'ERN members', '/members'],
            ['General Assembly 2026', 'Association', '2026-12-16', '14:00', '16:00', 'On Zoom', '/members'],
            ['ERNC27: registration deadline for presenters', 'Deadline', '2027-09-01', null, null, 'At least one author per contribution', '/conferences/ernc27'],
        ];

        foreach ($events as [$title, $kind, $date, $start, $end, $place, $link]) {
            Event::updateOrCreate(['title' => $title], [
                'kind' => $kind,
                'date' => $date,
                'all_day' => $start === null,
                'starts_at' => $start ? CarbonImmutable::parse($date.' '.$start, $zone)->utc() : null,
                'ends_at' => $end ? CarbonImmutable::parse($date.' '.$end, $zone)->utc() : null,
                'timezone' => $zone,
                'place' => $place,
                'link' => $link,
            ]);
        }
    }
}
