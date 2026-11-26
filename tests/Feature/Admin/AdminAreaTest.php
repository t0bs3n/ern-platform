<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\SystemValue;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAreaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Each tab with a role that may open it and one that may not (written out by hand).
     *
     * @return array<string, array{string, Role, Role}>
     */
    public static function tabs(): array
    {
        return [
            'overview' => ['/admin', Role::Editor, Role::Member],
            'members' => ['/admin/members', Role::Admin, Role::ConferenceTeam],
            'conference (team)' => ['/admin/conference', Role::ConferenceTeam, Role::Editor],
            'conference (reviewer)' => ['/admin/conference', Role::Reviewer, Role::DatabaseLead],
            'conference (final editor)' => ['/admin/conference', Role::FinalEditor, Role::LectureTeam],
            'database' => ['/admin/database', Role::DatabaseLead, Role::Editor],
            'teaching' => ['/admin/teaching', Role::LectureTeam, Role::Reviewer],
            'content' => ['/admin/content', Role::Editor, Role::DatabaseLead],
            'system' => ['/admin/system', Role::Admin, Role::ConferenceTeam],
        ];
    }

    #[DataProvider('tabs')]
    public function test_each_tab_is_refused_for_a_member_and_allowed_for_the_fitting_role(string $url, Role $allowed, Role $refused): void
    {
        $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
        $this->actingAs(User::factory()->withRoles($allowed)->create())->get($url)->assertOk()->assertSee('Admin area');
        $this->actingAs(User::factory()->withRoles($refused)->create())->get($url)->assertForbidden();
    }

    #[DataProvider('tabs')]
    public function test_a_guest_is_sent_to_sign_in(string $url): void
    {
        $this->get($url)->assertRedirect('/members/sign-in');
    }

    public function test_an_admin_sees_every_tab(): void
    {
        $response = $this->actingAs(User::factory()->withRoles(Role::Admin)->create())->get('/admin');

        $response->assertOk();
        foreach (['members', 'conference', 'database', 'teaching', 'content', 'system'] as $tab) {
            $response->assertSee('class="x-tab" href="'.url('/admin/'.$tab).'"', false);
        }
        $response->assertSee('Board and admins only')
            ->assertSee('Back to the member area');
    }

    public function test_the_tab_bar_shows_only_the_tabs_the_roles_allow(): void
    {
        $response = $this->actingAs(User::factory()->withRoles(Role::Editor)->create())->get('/admin');

        $response->assertSee('class="x-tab" href="'.url('/admin').'"', false)
            ->assertSee('class="x-tab" href="'.url('/admin/content').'"', false);
        foreach (['members', 'conference', 'database', 'teaching', 'system'] as $tab) {
            $response->assertDontSee('class="x-tab" href="'.url('/admin/'.$tab).'"', false);
        }

        $this->actingAs(User::factory()->withRoles(Role::Reviewer)->create())->get('/admin')
            ->assertSee('class="x-tab" href="'.url('/admin/conference').'"', false)
            ->assertDontSee('class="x-tab" href="'.url('/admin/content').'"', false);
    }

    public function test_the_current_tab_is_marked(): void
    {
        $this->actingAs(User::factory()->withRoles(Role::Editor)->create())->get('/admin/content')
            ->assertSee('aria-current="page">Content</a>', false);
    }

    public function test_an_unknown_tab_answers_404(): void
    {
        $this->actingAs(User::factory()->withRoles(Role::Admin)->create())->get('/admin/nothing')->assertNotFound();
    }

    public function test_the_system_page_shows_version_environment_and_last_run(): void
    {
        config(['ern.version' => '2026.11.26-test']);
        $admin = User::factory()->withRoles(Role::Admin)->create();

        $this->actingAs($admin)->get('/admin/system')
            ->assertSee('2026.11.26-test')
            ->assertSee('testing')
            ->assertSee('Not yet. The scheduler has not run.');

        $this->travelTo(now()->setDate(2026, 11, 26)->setTime(9, 15));
        $this->artisan('ern:heartbeat')->assertSuccessful();

        $this->actingAs($admin)->get('/admin/system')->assertSee('26 November 2026, 09:15 UTC');
    }

    public function test_the_heartbeat_command_stores_a_time(): void
    {
        $this->assertNull(SystemValue::time(SystemValue::LAST_SCHEDULED_RUN));

        $this->travelTo(now()->setDate(2026, 11, 26)->setTime(9, 15, 0));
        $this->artisan('ern:heartbeat')->assertSuccessful();

        $this->assertSame('2026-11-26 09:15', SystemValue::time(SystemValue::LAST_SCHEDULED_RUN)->format('Y-m-d H:i'));
    }

    public function test_the_heartbeat_is_scheduled_every_minute(): void
    {
        $events = collect(app(Schedule::class)->events())
            ->filter(fn ($event) => str_contains($event->command ?? '', 'ern:heartbeat'));

        $this->assertCount(1, $events);
        $this->assertSame('* * * * *', $events->first()->expression);
    }

    public function test_the_footer_links_the_admin_area_for_admin_roles_only(): void
    {
        $this->get('/')->assertDontSee('Admin area');
        $this->actingAs(User::factory()->create())->get('/')->assertDontSee('Admin area');
        $this->actingAs(User::factory()->withRoles(Role::Reviewer)->create())->get('/')
            ->assertSee('Admin area')
            ->assertSee('href="'.url('/admin').'"', false);
    }

    public function test_an_unapproved_admin_is_refused(): void
    {
        $admin = User::factory()->withRoles(Role::Admin)->create();
        $admin->forceFill(['approved_at' => null])->save();

        $this->actingAs($admin)->get('/admin')->assertForbidden();
    }
}
