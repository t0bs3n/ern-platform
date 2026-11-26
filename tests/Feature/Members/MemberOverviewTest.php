<?php

namespace Tests\Feature\Members;

use App\Enums\Role;
use App\Models\Event;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MemberOverviewTest extends TestCase
{
    use RefreshDatabase;

    private function notice(string $title, string $status = Notice::PUBLISHED): Notice
    {
        $notice = new Notice(['kind' => 'General Assembly', 'title' => $title, 'body' => 'On **Zoom**.', 'status' => $status]);
        $notice->published_at = $status === Notice::PUBLISHED ? now() : null;
        $notice->save();

        return $notice;
    }

    public function test_the_overview_shows_the_members_own_data(): void
    {
        $me = User::factory()->create([
            'first_name' => 'Maja', 'career_stage' => 'PhD student', 'institution' => 'Nordic University (example)',
            'country' => 'Sweden', 'listed_in_directory' => true, 'discord_name' => 'maja.example', 'approved_at' => '2025-03-10',
        ]);
        User::factory()->create(['first_name' => 'Someone', 'institution' => 'Other Place (example)']);

        $this->actingAs($me)->get('/members')->assertOk()
            ->assertSee('Welcome back, Maja')
            ->assertSee('PhD student · Nordic University (example)')
            ->assertSee('Your membership')
            ->assertSee('March 2025')
            ->assertSee('Nordic University (example), Sweden')
            ->assertSee('Visible to members')
            ->assertSee('maja.example')
            ->assertSee('Update my details')
            ->assertSee('Your roles')
            ->assertSee('Sees the member area')
            ->assertDontSee('Admin area</a>', false)
            ->assertDontSee('Other Place (example)');
    }

    public function test_the_overview_shows_published_notices_only(): void
    {
        $this->notice('General Assembly 2026');
        $this->notice('A draft notice', Notice::DRAFT);

        $this->actingAs(User::factory()->create())->get('/members')
            ->assertSee('id="notices"', false)
            ->assertSee('General Assembly 2026')
            ->assertSee('On <strong>Zoom</strong>.', false)
            ->assertDontSee('A draft notice');
    }

    public function test_the_overview_shows_the_next_events_and_leaves_out_empty_blocks(): void
    {
        $me = User::factory()->create();
        $this->actingAs($me)->get('/members')->assertDontSee('Coming up')->assertDontSee('id="notices"', false);

        Event::create(['title' => 'Members meeting', 'kind' => 'Association', 'date' => now()->addWeek()->toDateString(), 'all_day' => true, 'timezone' => 'Europe/Berlin']);
        $this->actingAs($me)->get('/members')->assertSee('Coming up')->assertSee('Members meeting');
    }

    public function test_people_with_admin_roles_see_their_roles_and_the_admin_link(): void
    {
        $this->actingAs(User::factory()->withRoles(Role::Editor)->create())->get('/members')
            ->assertSee('Editor')
            ->assertSee('Writes news, lectures and notices')
            ->assertSee('href="'.url('/admin').'"', false);
    }

    public function test_guests_and_unapproved_users_are_kept_out(): void
    {
        $this->get('/members')->assertRedirect('/members/sign-in');

        $user = User::factory()->create();
        $user->forceFill(['approved_at' => null])->save();
        $this->actingAs($user)->get('/members')->assertForbidden();
    }

    /** @return array<string, array{string, string}> */
    public static function noticeRoutes(): array
    {
        return [
            'new' => ['get', '/admin/content/notices/new'],
            'store' => ['post', '/admin/content/notices'],
            'edit' => ['get', '/admin/content/notices/{id}'],
            'update' => ['put', '/admin/content/notices/{id}'],
            'remove' => ['delete', '/admin/content/notices/{id}'],
        ];
    }

    #[DataProvider('noticeRoutes')]
    public function test_only_editors_change_notices(string $method, string $url): void
    {
        $notice = $this->notice('Kept notice');
        $url = str_replace('{id}', (string) $notice->id, $url);
        $payload = ['kind' => 'x', 'title' => 'Changed', 'body' => 'x', 'status' => 'published'];

        $this->{$method}($url, $payload)->assertRedirect('/members/sign-in');
        $this->actingAs(User::factory()->create())->{$method}($url, $payload)->assertForbidden();
        $this->actingAs(User::factory()->withRoles(Role::DatabaseLead)->create())->{$method}($url, $payload)->assertForbidden();

        $this->assertSame('Kept notice', $notice->fresh()->title);
    }

    public function test_an_editor_writes_publishes_and_removes_a_notice(): void
    {
        $editor = User::factory()->withRoles(Role::Editor)->create();

        $this->actingAs($editor)->get('/admin/content')->assertSee('Notices for members');
        $this->actingAs($editor)->post('/admin/content/notices', ['kind' => 'Association', 'title' => 'Statutes', 'body' => 'The statutes.', 'status' => 'draft'])
            ->assertRedirect('/admin/content');
        $notice = Notice::sole();
        $this->assertNull($notice->published_at);

        $this->actingAs($editor)->put('/admin/content/notices/'.$notice->id, ['kind' => 'Association', 'title' => 'Statutes', 'body' => 'The statutes.', 'status' => 'published']);
        $this->assertNotNull($notice->fresh()->published_at);
        $this->actingAs(User::factory()->create())->get('/members')->assertSee('Statutes');

        $this->actingAs($editor)->delete('/admin/content/notices/'.$notice->id)->assertRedirect('/admin/content');
        $this->assertDatabaseCount('notices', 0);
    }

    public function test_notice_input_is_checked(): void
    {
        $this->actingAs(User::factory()->withRoles(Role::Editor)->create())
            ->post('/admin/content/notices', ['status' => 'shouted', 'body' => '<script>x</script> ![x](https://tracker.example/p.png)'])
            ->assertSessionHasErrors(['kind' => 'Enter a label, for example General Assembly.', 'title' => 'Enter a title.', 'status', 'body']);
    }

    public function test_html_in_a_notice_is_shown_as_text(): void
    {
        $notice = new Notice(['kind' => 'x', 'title' => 'Escaped', 'body' => '<script>x</script>', 'status' => Notice::PUBLISHED]);
        $notice->published_at = now();
        $notice->save();

        $this->actingAs(User::factory()->create())->get('/members')
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>x', false);
    }

    public function test_setting_a_notice_back_to_draft_hides_it(): void
    {
        $editor = User::factory()->withRoles(Role::Editor)->create();
        $notice = $this->notice('Back to draft');

        $this->actingAs($editor)->put('/admin/content/notices/'.$notice->id, ['kind' => 'x', 'title' => 'Back to draft', 'body' => 'x', 'status' => 'draft']);

        $this->assertNull($notice->fresh()->published_at);
        $this->flushSession(); // the editor's "Saved: …" message is not part of this check
        $this->actingAs(User::factory()->create())->get('/members')->assertDontSee('Back to draft');
    }

    public function test_notices_never_reach_the_public_pages(): void
    {
        $this->notice('Members only notice');

        $this->get('/')->assertDontSee('Members only notice');
        $this->get('/news')->assertDontSee('Members only notice');
    }
}
