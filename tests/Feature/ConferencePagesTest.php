<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Conference;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ConferencePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ContentSeeder::class);
    }

    public function test_the_overview_shows_the_next_conference_and_all_past_ones(): void
    {
        $html = $this->get('/conferences')->assertOk()
            ->assertSee('<h1 style="margin-bottom:1.5rem">ERN Conference 2027</h1>', false)
            ->assertSee('9 to 11 November 2027')
            ->assertSee('NC State University, Raleigh, USA')
            ->assertSee('Abstract deadline:')
            ->assertSee('Monday 4 January 2027')
            ->assertSee('Read the Call for Abstracts')
            ->assertSee('Past ERN Conferences')
            ->getContent();

        foreach (['ERNC26', 'ERNC25', 'ERNC24', 'ERNC22', 'ERNC21'] as $name) {
            $this->assertStringContainsString('<h3>'.$name.'</h3>', $html);
        }
        $this->assertLessThan(strpos($html, '<h3>ERNC21</h3>'), strpos($html, '<h3>ERNC26</h3>'), 'Newest first');
        $this->assertStringContainsString('30 October to 1 November 2024', $html);
    }

    #[DataProvider('pastConferences')]
    public function test_each_past_conference_page_answers_with_its_summary(string $slug, string $expected): void
    {
        $this->get('/conferences/'.$slug)->assertOk()
            ->assertSee('Conference Overview')
            ->assertSee($expected)
            ->assertSee('Read the Book of Abstracts');
        $this->get('/conferences/'.$slug.'/summary')->assertOk();
    }

    /** @return array<string, array{string, string}> */
    public static function pastConferences(): array
    {
        return [
            'ERNC26' => ['ernc26', 'University of Tsukuba'],
            'ERNC25' => ['ernc25', 'Seth E. Jenny'],
            'ERNC24' => ['ernc24', 'Where Worlds Collide'],
            'ERNC22' => ['ernc22', 'Jönköping'],
            'ERNC21' => ['ernc21', 'Esports Health and Well-being Management'],
        ];
    }

    public function test_the_ernc27_call_shows_its_dates_and_the_submit_button(): void
    {
        foreach (['/conferences/ernc27', '/conferences/ernc27/call'] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('<h1>ERNC27: Think &amp; Do</h1>', false)
                ->assertSee('Where Research, Competition &amp; Community Converge', false)
                ->assertSee('At a glance')
                ->assertSee('4 January 2027')
                ->assertSee('<h2 id="sec-contribution-formats">Contribution formats</h2>', false)
                ->assertSee('href="#sec-key-dates"', false)
                ->assertSee('Submit an abstract');
        }
    }

    public function test_submit_leads_to_the_configured_form_on_the_old_site(): void
    {
        config(['ern.submission_url' => 'https://old.example.test/submission/']);

        $this->get('/conferences/ernc27/submit')->assertRedirect('https://old.example.test/submission/');
        $this->get('/conferences/ernc26/submit')->assertNotFound();
    }

    public function test_unknown_conferences_and_tabs_answer_404(): void
    {
        $this->get('/conferences/ernc99')->assertNotFound();
        $this->get('/conferences/ernc27/programme')->assertNotFound();
        $this->get('/conferences/ernc26/book')->assertNotFound();
    }

    public function test_the_start_page_follows_the_conference(): void
    {
        $this->get('/')->assertSee('href="'.url('/conferences/ernc26').'"', false)->assertSee('ERNC27 Details')->assertSee('Featured Event')->assertSee('9 to 11 November 2027');
    }

    public function test_the_dates_text_covers_every_case(): void
    {
        $c = new Conference;
        foreach ([
            ['2027-11-09', '2027-11-11', '9 to 11 November 2027'],
            ['2024-10-30', '2024-11-01', '30 October to 1 November 2024'],
            ['2026-12-31', '2027-01-02', '31 December 2026 to 2 January 2027'],
            ['2026-05-05', '2026-05-05', '5 May 2026'],
        ] as [$start, $end, $text]) {
            $c->start_date = $start;
            $c->end_date = $end;
            $this->assertSame($text, $c->datesText());
        }
    }

    public function test_a_conference_team_member_edits_the_dates(): void
    {
        $team = User::factory()->withRoles(Role::ConferenceTeam)->create();
        $conference = Conference::where('slug', 'ernc27')->firstOrFail();

        $this->actingAs($team)->get('/admin/conference')->assertOk()->assertSee('ERNC27');
        $this->actingAs($team)->get('/admin/conference/ernc27')->assertOk()->assertSee('Edit ERNC27');

        $this->actingAs($team)->put('/admin/conference/ernc27', [
            'theme' => $conference->theme, 'place' => $conference->place, 'mode' => 'Hybrid', 'host' => 'NC State University',
            'start_date' => '2027-11-10', 'end_date' => '2027-11-12', 'status' => 'upcoming',
            'key_dates' => [
                ['label' => 'Abstract deadline', 'date' => '2027-01-11'],
                ['label' => '', 'date' => ''],
                ['label' => 'Submissions open', 'date' => '2026-11-02'],
            ],
        ])->assertRedirect('/admin/conference');

        $conference->refresh();
        $this->assertSame('10 to 12 November 2027', $conference->datesText());
        $this->assertSame([
            ['label' => 'Submissions open', 'date' => '2026-11-02'],
            ['label' => 'Abstract deadline', 'date' => '2027-01-11'],
        ], $conference->key_dates);

        $this->get('/conferences')->assertSee('10 to 12 November 2027')->assertSee('Monday 11 January 2027');
    }

    public function test_wrong_dates_are_refused(): void
    {
        $this->actingAs(User::factory()->withRoles(Role::ConferenceTeam)->create())->put('/admin/conference/ernc27', [
            'theme' => 'x', 'place' => 'x', 'mode' => 'x', 'start_date' => '2027-11-12', 'end_date' => '2027-11-10', 'status' => 'upcoming',
            'key_dates' => [['label' => 'No date', 'date' => '']],
        ])->assertSessionHasErrors([
            'end_date' => 'The last day cannot be before the first day.',
            'key_dates.0.date' => 'Enter the date.',
        ]);
    }

    public function test_members_and_other_roles_cannot_edit_conferences(): void
    {
        $payload = ['theme' => 'Changed', 'place' => 'x', 'mode' => 'x', 'start_date' => '2027-01-01', 'end_date' => '2027-01-02', 'status' => 'upcoming'];

        $this->put('/admin/conference/ernc27', $payload)->assertRedirect('/members/sign-in');
        foreach ([User::factory()->create(), User::factory()->withRoles(Role::Editor)->create(), User::factory()->withRoles(Role::Reviewer)->create()] as $user) {
            $this->actingAs($user)->get('/admin/conference/ernc27')->assertForbidden();
            $this->actingAs($user)->put('/admin/conference/ernc27', $payload)->assertForbidden();
        }
        $this->actingAs(User::factory()->create())->get('/admin/conference')->assertForbidden();

        $this->assertNotSame('Changed', Conference::where('slug', 'ernc27')->value('theme'));
    }

    public function test_reviewers_and_the_final_editor_see_the_tab_without_the_list(): void
    {
        foreach ([Role::Reviewer, Role::FinalEditor] as $role) {
            $this->actingAs(User::factory()->withRoles($role)->create())->get('/admin/conference')
                ->assertOk()
                ->assertDontSee('/admin/conference/ernc27');
        }
        $this->actingAs(User::factory()->withRoles(Role::Editor)->create())->get('/admin/conference')->assertForbidden();
    }

    public function test_an_admin_edits_a_conference_and_a_wrong_status_is_refused(): void
    {
        $admin = User::factory()->withRoles(Role::Admin)->create();
        $payload = ['theme' => 'A theme', 'place' => 'A place', 'mode' => 'Online', 'start_date' => '2027-11-09', 'end_date' => '2027-11-11'];

        $this->actingAs($admin)->put('/admin/conference/ernc27', $payload + ['status' => 'cancelled'])->assertSessionHasErrors('status');
        $this->actingAs($admin)->put('/admin/conference/ernc27', $payload + ['status' => 'upcoming'])->assertRedirect('/admin/conference');

        $this->assertSame('A theme', Conference::where('slug', 'ernc27')->value('theme'));
    }
}
