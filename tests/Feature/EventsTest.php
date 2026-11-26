<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Conference;
use App\Models\Event;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EventsTest extends TestCase
{
    use RefreshDatabase;

    private function event(string $title, string $date, ?string $start = null, ?string $end = null, string $zone = 'Europe/Oslo', string $kind = 'Association'): Event
    {
        return Event::create([
            'title' => $title, 'kind' => $kind, 'date' => $date, 'all_day' => $start === null,
            'starts_at' => $start ? CarbonImmutable::parse($date.' '.$start, $zone)->utc() : null,
            'ends_at' => $end ? CarbonImmutable::parse($date.' '.$end, $zone)->utc() : null,
            'timezone' => $zone, 'place' => 'On Zoom',
        ]);
    }

    public function test_the_list_is_ordered_hides_past_entries_and_includes_conference_dates(): void
    {
        $this->travelTo('2026-12-01 12:00');
        $this->seed(ContentSeeder::class);
        $this->event('Old meeting', '2026-11-20');
        $this->event('General Assembly 2026', '2026-12-16', '14:00', '16:00');
        $this->event('A deadline today', '2026-12-01');

        $response = $this->get('/events')->assertOk()
            ->assertSee('<h1>Events and deadlines</h1>', false)
            ->assertDontSee('Old meeting')
            ->assertDontSee('ERNC27: submissions open')
            ->assertSee('Wednesday 16 December 2026, 14:00 to 16:00 (Oslo time)')
            ->assertSee('ERNC27: abstract deadline')
            ->assertSee('Monday 4 January 2027')
            ->assertSee('9 to 11 November 2027');

        $response->assertSeeInOrder(['A deadline today', 'General Assembly 2026', 'ERNC27: abstract deadline', 'ERNC27: decisions', 'ERNC27: Think &amp; Do'], false);
    }

    public function test_the_list_filters_by_kind_without_javascript(): void
    {
        $this->seed(ContentSeeder::class);
        $this->event('General Assembly', now()->addMonth()->toDateString(), '14:00');

        $this->get('/events?kind=Association')->assertOk()
            ->assertSee('General Assembly')
            ->assertDontSee('ERNC27: abstract deadline')
            ->assertSee('aria-current="page">Association</a>', false);

        $this->get('/events?kind=Deadline')->assertDontSee('General Assembly');
        $this->get('/events?kind=Nonsense')->assertOk()->assertSee('General Assembly');
    }

    public function test_the_ics_feed_is_valid_and_carries_the_time_zones(): void
    {
        $this->travelTo('2026-12-01 12:00');
        $this->seed(ContentSeeder::class);
        $this->event('General Assembly 2026, with "quotes"; and commas', '2026-12-16', '14:00', '16:00');
        $this->event('Seminar in New York', '2027-03-20', '10:00', null, 'America/New_York', 'Lecture');

        $response = $this->get('/events.ics')->assertOk();
        $this->assertStringStartsWith('text/calendar', $response->headers->get('Content-Type'));
        $ics = $response->getContent();

        $this->assertStringStartsWith("BEGIN:VCALENDAR\r\nVERSION:2.0\r\n", $ics);
        $this->assertStringEndsWith("END:VCALENDAR\r\n", $ics);
        $this->assertSame(substr_count($ics, 'BEGIN:VEVENT'), substr_count($ics, 'END:VEVENT'));
        $this->assertStringContainsString("BEGIN:VTIMEZONE\r\nTZID:Europe/Oslo", $ics);
        $this->assertStringContainsString("BEGIN:VTIMEZONE\r\nTZID:America/New_York", $ics);
        $this->assertStringContainsString('DTSTART;TZID=Europe/Oslo:20261216T140000', $ics);
        $this->assertStringContainsString('DTEND;TZID=Europe/Oslo:20261216T160000', $ics);
        $this->assertStringContainsString('DTSTART;TZID=America/New_York:20270320T100000', $ics);
        $this->assertStringContainsString('TZOFFSETTO:+0100', $ics);
        $this->assertStringContainsString('TZOFFSETTO:-0400', $ics);
        $this->assertStringContainsString('DTSTART;VALUE=DATE:20270104', $ics);
        $this->assertStringContainsString('DTEND;VALUE=DATE:20270105', $ics);
        $this->assertStringContainsString('DTSTART;VALUE=DATE:20271109', $ics);
        $this->assertStringContainsString('DTEND;VALUE=DATE:20271112', $ics);
        $this->assertStringContainsString('SUMMARY:General Assembly 2026\, with "quotes"\; and commas', $ics);

        // Every line ends with CRLF and is at most 75 bytes.
        foreach (explode("\r\n", rtrim($ics, "\r\n")) as $line) {
            $this->assertLessThanOrEqual(75, strlen($line), $line);
            $this->assertStringNotContainsString("\n", $line);
        }
        // Every VEVENT has a UID and a DTSTAMP.
        preg_match_all('/BEGIN:VEVENT(.*?)END:VEVENT/s', $ics, $events);
        foreach ($events[1] as $event) {
            $this->assertMatchesRegularExpression('/\r\nUID:[^\r]+@/', $event);
            $this->assertMatchesRegularExpression('/\r\nDTSTAMP:\d{8}T\d{6}Z/', $event);
        }
    }

    public function test_entries_are_ordered_by_when_they_begin_across_time_zones(): void
    {
        $this->event('Berlin at 11', '2027-03-20', '11:00', null, 'Europe/Berlin');
        $this->event('New York at 10', '2027-03-20', '10:00', null, 'America/New_York');
        $this->event('Berlin at 9', '2027-03-20', '09:00', null, 'Europe/Berlin');

        $this->get('/events')->assertSeeInOrder(['Berlin at 9', 'Berlin at 11', 'New York at 10']);
    }

    public function test_links_in_the_feed_are_absolute_and_dangerous_links_are_refused(): void
    {
        $this->event('With a link', now()->addDay()->toDateString())->update(['link' => '/members']);
        $ics = $this->get('/events.ics')->getContent();
        $this->assertStringContainsString('URL:'.url('/members'), $ics);

        $editor = User::factory()->withRoles(Role::Editor)->create();
        foreach (["https://a.example/\r\nBEGIN:VEVENT\r\nSUMMARY:x", "https://a.example/\nX", '/\\evil.example', '//evil.example', 'https://a b.example', "https://a\x00b.example", "/a\x7Fb"] as $link) {
            $this->actingAs($editor)->post('/admin/content/events', [
                'title' => 'Bad link', 'kind' => 'Association', 'date' => '2027-01-02', 'timezone' => 'UTC', 'link' => $link,
            ])->assertSessionHasErrors('link');
        }
        $this->assertDatabaseMissing('events', ['title' => 'Bad link']);

        // Even if a bad link got into the database, the feed leaves it out.
        $this->event('Injected', now()->addDay()->toDateString())->update(['link' => "https://a.example/\r\nBEGIN:VEVENT"]);
        $ics = $this->get('/events.ics')->getContent();
        $this->assertSame(substr_count($ics, 'BEGIN:VEVENT'), substr_count($ics, 'END:VEVENT'));
        $this->assertStringNotContainsString('URL:https://a.example/', $ics);
    }

    public function test_a_zone_without_summer_time_still_gets_its_offset(): void
    {
        $this->event('Seminar in Tokyo', now()->addMonth()->toDateString(), '10:00', null, 'Asia/Tokyo');

        $ics = $this->get('/events.ics')->getContent();

        $this->assertStringContainsString('TZID:Asia/Tokyo', $ics);
        $this->assertStringContainsString('TZOFFSETTO:+0900', $ics);
    }

    public function test_key_dates_with_the_same_label_keep_different_ids(): void
    {
        $this->seed(ContentSeeder::class);
        Conference::where('slug', 'ernc27')->update(['key_dates' => json_encode([
            ['label' => 'Deadline', 'date' => now()->addMonth()->toDateString()],
            ['label' => 'Deadline', 'date' => now()->addMonths(2)->toDateString()],
        ])]);

        preg_match_all('/UID:(conference-ernc27-[^@]+)@/', $this->get('/events.ics')->getContent(), $uids);

        $this->assertSame(count($uids[1]), count(array_unique($uids[1])));
        $this->assertCount(2, $uids[1]);
    }

    public function test_long_lines_are_folded_without_splitting_characters(): void
    {
        $this->event(str_repeat('Ü', 60), now()->addDay()->toDateString());

        $ics = $this->get('/events.ics')->getContent();
        $unfolded = str_replace("\r\n ", '', $ics);

        $this->assertStringContainsString('SUMMARY:'.str_repeat('Ü', 60), $unfolded);
        $this->assertTrue(mb_check_encoding($ics, 'UTF-8'));
    }

    public function test_an_editor_adds_changes_and_removes_an_event(): void
    {
        $editor = User::factory()->withRoles(Role::Editor)->create();

        $this->actingAs($editor)->get('/admin/content')->assertSee('Events and deadlines');
        $this->actingAs($editor)->post('/admin/content/events', [
            'title' => 'General Assembly 2027', 'kind' => 'Association', 'date' => '2027-12-15',
            'start_time' => '14:00', 'end_time' => '16:00', 'timezone' => 'Europe/Oslo', 'place' => 'On Zoom', 'link' => '/members',
        ])->assertRedirect('/admin/content');

        $event = Event::firstOrFail();
        $this->assertSame('2027-12-15 13:00:00', $event->starts_at->utc()->format('Y-m-d H:i:s'), 'Stored in UTC');
        $this->assertFalse($event->all_day);

        // Stored as 13:00 UTC, shown back as 14:00 Oslo time.
        $this->actingAs($editor)->get('/admin/content/events/'.$event->id)->assertSee('value="14:00"', false)->assertSee('value="16:00"', false);

        $this->actingAs($editor)->put('/admin/content/events/'.$event->id, [
            'title' => 'General Assembly 2027', 'kind' => 'Association', 'date' => '2027-12-15', 'timezone' => 'Europe/Oslo',
        ])->assertRedirect('/admin/content');
        $this->assertTrue($event->fresh()->all_day);
        $this->assertNull($event->fresh()->starts_at);

        $this->actingAs($editor)->delete('/admin/content/events/'.$event->id)->assertRedirect('/admin/content');
        $this->assertDatabaseCount('events', 0);
    }

    public function test_wrong_event_input_is_refused(): void
    {
        $this->actingAs(User::factory()->withRoles(Role::Editor)->create())->post('/admin/content/events', [
            'kind' => 'Party', 'date' => 'soon', 'start_time' => '16:00', 'end_time' => '15:00', 'timezone' => 'Mars/Olympus', 'link' => 'javascript:alert(1)',
        ])->assertSessionHasErrors([
            'title' => 'Enter a title.',
            'kind' => 'Choose the kind of event.',
            'date' => 'Enter a valid date.',
            'timezone' => 'Choose a time zone from the list.',
            'link' => 'Enter a web address starting with https:// or an address on this site starting with /.',
        ]);

        $this->assertDatabaseCount('events', 0);
    }

    /** @return array<string, array{string, string}> */
    public static function eventRoutes(): array
    {
        return [
            'new' => ['get', '/admin/content/events/new'],
            'store' => ['post', '/admin/content/events'],
            'edit' => ['get', '/admin/content/events/{id}'],
            'update' => ['put', '/admin/content/events/{id}'],
            'remove' => ['delete', '/admin/content/events/{id}'],
        ];
    }

    #[DataProvider('eventRoutes')]
    public function test_members_and_other_roles_cannot_change_events(string $method, string $url): void
    {
        $event = $this->event('Kept event', '2027-01-01');
        $url = str_replace('{id}', (string) $event->id, $url);
        $payload = ['title' => 'Changed', 'kind' => 'Association', 'date' => '2027-01-02', 'timezone' => 'UTC'];

        $this->{$method}($url, $payload)->assertRedirect('/members/sign-in');
        $this->actingAs(User::factory()->create())->{$method}($url, $payload)->assertForbidden();
        $this->actingAs(User::factory()->withRoles(Role::LectureTeam)->create())->{$method}($url, $payload)->assertForbidden();

        $this->assertSame('Kept event', $event->fresh()->title);
    }
}
