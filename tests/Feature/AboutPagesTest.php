<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Page;
use App\Models\TeamMember;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AboutPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_us_shows_the_seeded_content(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/about')->assertOk()
            ->assertSee('<h1>About the Esports Research Network</h1>', false)
            ->assertSee('Our Goals')
            ->assertSee('Connect Researchers Worldwide')
            ->assertSee('683')
            ->assertSee('members in 29 European countries')
            ->assertSee('As of: 31 October 2026')
            ->assertSee('members.svg')
            ->assertSee('Mission Statement')
            ->assertSee('Board Members')
            ->assertSee('Tobias Scholz')
            ->assertSee('Chair')
            ->assertSee('Governance &amp; Volunteers', false)
            ->assertSee('Our History')
            ->assertSee('Former ERN Board Members')
            ->assertSee('href="#sec-board"', false)
            ->assertDontSee('candidate');
    }

    public function test_the_ombudsperson_is_not_named(): void
    {
        $this->seed(ContentSeeder::class);

        $this->assertFalse(TeamMember::where('role_title', 'Ombudsperson')->exists());
        $this->get('/about')->assertDontSee('Oliver Leis');
    }

    public function test_an_empty_order_keeps_the_place_when_editing(): void
    {
        $person = TeamMember::create(['group' => 'board', 'name' => 'Placed Person', 'role_title' => 'Board Member', 'position' => 50]);

        $this->actingAs(User::factory()->withRoles(Role::Editor)->create())->put('/admin/content/people/'.$person->id, [
            'group' => 'board', 'name' => 'Placed Person', 'role_title' => 'Board Member', 'position' => '',
        ])->assertRedirect('/admin/content');

        $this->assertSame(50, $person->fresh()->position);
    }

    public function test_get_involved_shows_the_seeded_content(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/get-involved')->assertOk()
            ->assertSee('<h1>Get Involved</h1>', false)
            ->assertSee('Become a Member')
            ->assertSee('A Community-Led Network')
            ->assertSee('Ways to Participate')
            ->assertSee('Help host informal coffee chats')
            ->assertSee('How to Get Started')
            ->assertSee('Contact the board: board@esportsresearch.net')
            ->assertSee('id="sec-ways"', false);
    }

    public function test_the_pages_work_without_seeded_content(): void
    {
        $this->get('/about')->assertOk();
        $this->get('/get-involved')->assertOk();
    }

    public function test_the_board_is_shown_in_the_chosen_order(): void
    {
        TeamMember::create(['group' => 'board', 'name' => 'Second Person', 'role_title' => 'Board Member', 'position' => 20]);
        TeamMember::create(['group' => 'board', 'name' => 'First Person', 'role_title' => 'Chair', 'position' => 10]);

        $html = $this->get('/about')->getContent();

        $this->assertLessThan(strpos($html, 'Second Person'), strpos($html, 'First Person'));
    }

    public function test_text_blocks_follow_an_editors_change(): void
    {
        $this->seed(ContentSeeder::class);
        Page::where('slug', 'about-mission')->update(['body' => 'Our **new** mission.']);

        $this->get('/about')->assertSee('Our <strong>new</strong> mission.', false);
    }

    public function test_an_editor_adds_changes_and_removes_a_person(): void
    {
        $editor = User::factory()->withRoles(Role::Editor)->create();

        $this->actingAs($editor)->get('/admin/content')->assertOk()->assertSee('Board and volunteers');

        $this->actingAs($editor)->post('/admin/content/people', [
            'group' => 'board', 'name' => 'New Person', 'affiliation' => 'Example University (example)', 'role_title' => 'Board Member',
        ])->assertRedirect('/admin/content');

        $person = TeamMember::firstOrFail();
        $this->get('/about')->assertSee('New Person');

        $this->actingAs($editor)->put('/admin/content/people/'.$person->id, [
            'group' => 'governance', 'name' => 'New Person', 'role_title' => 'Communications Lead',
        ])->assertRedirect('/admin/content');
        $this->assertSame('governance', $person->fresh()->group);

        $this->actingAs($editor)->delete('/admin/content/people/'.$person->id)->assertRedirect('/admin/content');
        $this->assertDatabaseCount('team_members', 0);
        $this->get('/about')->assertDontSee('New Person');
    }

    public function test_people_need_a_name_role_and_valid_group(): void
    {
        $this->actingAs(User::factory()->withRoles(Role::Editor)->create())
            ->post('/admin/content/people', ['group' => 'candidates'])
            ->assertSessionHasErrors(['name' => 'Enter the name.', 'role_title' => 'Enter the role, for example Board Member.', 'group' => 'Choose where the person is shown.']);
    }

    /** @return array<string, array{string, string}> */
    public static function peopleRoutes(): array
    {
        return [
            'new' => ['get', '/admin/content/people/new'],
            'store' => ['post', '/admin/content/people'],
            'edit' => ['get', '/admin/content/people/{id}'],
            'update' => ['put', '/admin/content/people/{id}'],
            'remove' => ['delete', '/admin/content/people/{id}'],
        ];
    }

    #[DataProvider('peopleRoutes')]
    public function test_members_and_other_roles_cannot_change_people(string $method, string $url): void
    {
        $person = TeamMember::create(['group' => 'board', 'name' => 'Kept Person', 'role_title' => 'Chair']);
        $url = str_replace('{id}', (string) $person->id, $url);
        $payload = ['group' => 'board', 'name' => 'Changed', 'role_title' => 'x'];

        $this->{$method}($url, $payload)->assertRedirect('/members/sign-in');
        $this->actingAs(User::factory()->create())->{$method}($url, $payload)->assertForbidden();
        $this->actingAs(User::factory()->withRoles(Role::ConferenceTeam)->create())->{$method}($url, $payload)->assertForbidden();

        $this->assertSame('Kept Person', $person->fresh()->name);
    }
}
