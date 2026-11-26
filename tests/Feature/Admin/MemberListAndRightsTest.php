<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Members\ChangeRoles;
use App\Members\DecisionException;
use App\Members\MemberListCsv;
use App\Models\MemberAdminLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MemberListAndRightsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->withRoles(Role::Admin)->create(['first_name' => 'Ann', 'last_name' => 'Admin']);
        $this->member = User::factory()->create([
            'first_name' => 'Lukas', 'last_name' => 'Brandt', 'email' => 'lukas@example.test', 'country' => 'Germany',
            'institution' => 'Rhein University (example)', 'career_stage' => 'Professor',
        ]);
    }

    private function roles(User $user): array
    {
        return $user->fresh()->roles->map(fn ($r) => $r->role->value)->sort()->values()->all();
    }

    /** @return array<string, array{string, string}> */
    public static function addresses(): array
    {
        return [
            'list' => ['get', '/admin/members'],
            'member' => ['get', '/admin/members/{id}'],
            'roles' => ['put', '/admin/members/{id}/roles'],
            'export' => ['post', '/admin/members/export'],
        ];
    }

    #[DataProvider('addresses')]
    public function test_only_admins_get_in(string $method, string $url): void
    {
        $url = str_replace('{id}', (string) $this->member->id, $url);
        $payload = ['roles' => ['admin']];

        $this->{$method}($url, $payload)->assertRedirect('/members/sign-in');
        foreach ([User::factory()->create(), User::factory()->withRoles(Role::Editor)->create(), User::factory()->withRoles(Role::ConferenceTeam)->create()] as $user) {
            $this->actingAs($user)->{$method}($url, $payload)->assertForbidden();
        }
        $unapprovedAdmin = User::factory()->withRoles(Role::Admin)->create();
        $unapprovedAdmin->forceFill(['approved_at' => null])->save();
        $this->actingAs($unapprovedAdmin)->{$method}($url, $payload)->assertForbidden();

        $this->assertSame(['member'], $this->roles($this->member));
        $this->assertDatabaseCount('member_admin_log', 0);
    }

    public function test_the_list_shows_the_mockup_columns_and_no_e_mail(): void
    {
        $this->actingAs($this->admin)->get('/admin/members')->assertOk()
            ->assertSee('Member list')
            ->assertSee('Export the list')
            ->assertSee('<th>Name</th><th>Status</th><th>Country</th><th>Role on the platform</th>', false)
            ->assertSee('Lukas Brandt')
            ->assertSee('Professor')
            ->assertSee('Edit rights')
            ->assertSee('2 members')
            ->assertDontSee('lukas@example.test')
            ->assertDontSee('Write to all members');
    }

    public function test_search_finds_by_name_country_and_institution_and_escapes_wildcards(): void
    {
        $see = fn (string $q) => $this->actingAs($this->admin)->get('/admin/members?q='.urlencode($q));

        $see('brandt')->assertSee('Lukas Brandt')->assertSee('1 of 2 members')->assertDontSee('<td>Ann Admin', false);
        $see('germany')->assertSee('Lukas Brandt');
        $see('rhein')->assertSee('Lukas Brandt');
        $see('lukas@example.test')->assertSee('No member found.');
        $see('%')->assertSee('No member found.');
    }

    public function test_the_search_is_kept_when_paging(): void
    {
        User::factory()->count(55)->create(['country' => 'Norway']);

        $page = $this->actingAs($this->admin)->get('/admin/members?q=norway')->assertSee('Next')->getContent();
        $this->assertStringContainsString('q=norway', $page);
    }

    public function test_unapproved_users_are_not_listed_and_answer_404(): void
    {
        $applicant = User::factory()->unapproved()->create(['first_name' => 'Pending']);

        $this->actingAs($this->admin)->get('/admin/members')->assertDontSee('Pending');
        $this->actingAs($this->admin)->get('/admin/members/'.$applicant->id)->assertNotFound();
        $this->actingAs($this->admin)->put('/admin/members/'.$applicant->id.'/roles', ['roles' => ['admin']])->assertNotFound();
        $this->assertFalse($applicant->fresh()->roles->contains(fn ($r) => $r->role === Role::Admin));
    }

    public function test_a_request_to_end_the_membership_is_shown(): void
    {
        $this->member->forceFill(['membership_end_requested_at' => '2026-11-20 10:00:00'])->save();

        $this->actingAs($this->admin)->get('/admin/members')->assertSee('Asked to end the membership on 20 November 2026');
        $this->actingAs($this->admin)->get('/admin/members/'.$this->member->id)->assertSee('Asked to end the membership on 20 November 2026');
    }

    public function test_an_admin_gives_and_removes_roles_and_each_change_is_logged(): void
    {
        $this->actingAs($this->admin)->get('/admin/members/'.$this->member->id)->assertOk()
            ->assertSee('Rights of Lukas Brandt')
            ->assertSee('Save the rights')
            ->assertSee('Gives the final result on every submission. One person holds this role.');

        $this->actingAs($this->admin)->put('/admin/members/'.$this->member->id.'/roles', ['roles' => ['conference_team', 'reviewer']])
            ->assertRedirect('/admin/members/'.$this->member->id)
            ->assertSessionHas('status', 'Rights saved for Lukas Brandt: Member, Reviewer, Conference team.');
        $this->assertSame(['conference_team', 'member', 'reviewer'], $this->roles($this->member));
        $this->assertSame(2, MemberAdminLog::where('action', MemberAdminLog::ROLE_GIVEN)->count());

        $this->actingAs($this->admin)->put('/admin/members/'.$this->member->id.'/roles', ['roles' => ['reviewer']]);
        $this->assertSame(['member', 'reviewer'], $this->roles($this->member));
        $log = MemberAdminLog::where('action', MemberAdminLog::ROLE_REMOVED)->sole();
        $this->assertSame([$this->admin->id, $this->member->id, 'conference_team'], [$log->actor_id, $log->member_id, $log->role]);
        $this->assertNotNull($log->created_at);

        // Saving the same set again changes and logs nothing.
        $this->actingAs($this->admin)->put('/admin/members/'.$this->member->id.'/roles', ['roles' => ['reviewer']]);
        $this->assertSame(3, MemberAdminLog::count());
    }

    public function test_the_member_role_always_stays_and_unknown_roles_are_refused(): void
    {
        $this->actingAs($this->admin)->put('/admin/members/'.$this->member->id.'/roles', ['roles' => []]);
        $this->assertSame(['member'], $this->roles($this->member));

        $this->actingAs($this->admin)->put('/admin/members/'.$this->member->id.'/roles', ['roles' => ['member', 'member']]);
        $this->assertSame(['member'], $this->roles($this->member));

        $this->actingAs($this->admin)->put('/admin/members/'.$this->member->id.'/roles', ['roles' => ['emperor']])
            ->assertSessionHasErrors(['roles.0' => 'Choose roles from the list.']);
        $this->assertSame(['member'], $this->roles($this->member));
    }

    public function test_a_role_list_that_is_not_a_list_is_refused(): void
    {
        $this->actingAs($this->admin)->put('/admin/members/'.$this->member->id.'/roles', ['roles' => 'admin'])
            ->assertSessionHasErrors(['roles' => 'Choose roles from the list.']);
        $this->assertSame(['member'], $this->roles($this->member));
    }

    public function test_giving_the_final_editor_role_when_nobody_holds_it(): void
    {
        $this->actingAs($this->admin)->put('/admin/members/'.$this->member->id.'/roles', ['roles' => ['final_editor']])
            ->assertSessionHas('status', 'Rights saved for Lukas Brandt: Member, Final editor.');
        $this->assertSame(['final_editor', 'member'], $this->roles($this->member));
    }

    public function test_an_admin_cannot_remove_their_own_admin_role(): void
    {
        User::factory()->withRoles(Role::Admin)->create();

        $this->actingAs($this->admin)->put('/admin/members/'.$this->admin->id.'/roles', ['roles' => []])
            ->assertSessionHasErrors(['roles' => 'You cannot remove your own admin role. Ask another admin to do it.']);
        $this->assertContains('admin', $this->roles($this->admin));
    }

    public function test_the_last_admin_cannot_be_removed_but_another_can_while_one_remains(): void
    {
        $second = User::factory()->withRoles(Role::Admin)->create();

        $this->actingAs($this->admin)->put('/admin/members/'.$second->id.'/roles', ['roles' => []])->assertSessionHasNoErrors();
        $this->assertNotContains('admin', $this->roles($second));

        // Through the action: the only admin left cannot lose the role, whoever asks.
        $this->expectException(DecisionException::class);
        $this->expectExceptionMessage('The platform needs at least one admin. Give the role to someone else first.');
        app(ChangeRoles::class)($second->fresh(), $this->admin, []);
    }

    public function test_two_admins_cannot_remove_each_other_one_after_the_other(): void
    {
        $second = User::factory()->withRoles(Role::Admin)->create();

        $this->actingAs($this->admin)->put('/admin/members/'.$second->id.'/roles', ['roles' => []]);
        $this->actingAs($second->fresh())->put('/admin/members/'.$this->admin->id.'/roles', ['roles' => []])->assertForbidden();

        $this->assertContains('admin', $this->roles($this->admin));
    }

    public function test_the_final_editor_role_moves_to_the_new_holder(): void
    {
        $current = User::factory()->withRoles(Role::FinalEditor)->create(['first_name' => 'Fiona', 'last_name' => 'Final']);

        $this->actingAs($this->admin)->put('/admin/members/'.$this->member->id.'/roles', ['roles' => ['final_editor', 'admin']])
            ->assertSessionHas('status', 'Rights saved for Lukas Brandt: Member, Final editor, Admin. Final editor was taken from Fiona Final, because one person holds this role.');

        $this->assertSame(['admin', 'final_editor', 'member'], $this->roles($this->member));
        $this->assertSame(['member'], $this->roles($current));
        $this->assertTrue(MemberAdminLog::where(['member_id' => $current->id, 'action' => MemberAdminLog::ROLE_REMOVED, 'role' => 'final_editor'])->exists());
        $this->assertTrue(MemberAdminLog::where(['member_id' => $this->member->id, 'action' => MemberAdminLog::ROLE_GIVEN, 'role' => 'final_editor'])->exists());
    }

    public function test_the_log_holds_no_names_or_addresses_and_survives_deletions(): void
    {
        $this->actingAs($this->admin)->put('/admin/members/'.$this->member->id.'/roles', ['roles' => ['editor']]);

        $row = json_encode(MemberAdminLog::sole()->getAttributes());
        foreach (['Lukas', 'Brandt', 'lukas@example.test', 'Ann'] as $personal) {
            $this->assertStringNotContainsString($personal, $row);
        }

        $this->member->delete();
        $this->assertNull(MemberAdminLog::sole()->member_id);
        $this->assertSame($this->admin->id, MemberAdminLog::sole()->actor_id);
    }

    public function test_a_removed_role_takes_effect_on_the_next_request(): void
    {
        $editor = User::factory()->withRoles(Role::Editor)->create();
        $this->actingAs($editor)->get('/admin/content')->assertOk();

        $this->actingAs($this->admin)->put('/admin/members/'.$editor->id.'/roles', ['roles' => []]);

        $this->actingAs($editor->fresh())->get('/admin/content')->assertForbidden();
        $this->assertFalse(Gate::forUser($editor->fresh())->allows('edit-content'));
    }

    public function test_the_formula_guard_also_catches_leading_spaces_and_full_width_signs(): void
    {
        foreach (['  =1+1', "\u{3000}+1", "\u{00A0}=1", '＝SUM(A1)', '＋1', '－1', '＠x', "\t=1", "\tabc", "\rabc"] as $value) {
            $this->assertSame("'".$value, MemberListCsv::safe($value), json_encode($value));
        }
        foreach (['Anna', 'a=b', '', 'Universität (example)', 'No. 1 - first', 'Ålesund', 'Ñandú (example)', '東京大学'] as $value) {
            $this->assertSame($value, MemberListCsv::safe($value), $value);
        }
    }

    public function test_the_export_is_a_logged_csv_with_the_agreed_columns_and_no_e_mail(): void
    {
        $this->member->forceFill(['membership_end_requested_at' => '2026-11-20 10:00:00'])->save();
        User::factory()->unapproved()->create(['first_name' => 'Pending']);
        User::factory()->create(['first_name' => '=HYPERLINK("http://evil.example")', 'institution' => '@SUM(1)', 'country' => '-2+3']);

        $response = $this->actingAs($this->admin)->post('/admin/members/export')->assertOk();
        $csv = $response->streamedContent();

        $this->assertStringContainsString('attachment; filename=ern-members-', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringStartsWith("\xEF\xBB\xBF".implode(',', array_map(fn ($c) => str_contains($c, ' ') ? '"'.$c.'"' : $c, MemberListCsv::COLUMNS)), $csv);
        $this->assertStringContainsString('Lukas,Brandt,Professor', $csv);
        $this->assertStringContainsString('2026-11-20', $csv);
        $this->assertStringNotContainsString('@example.test', $csv);
        $this->assertStringNotContainsString('Pending', $csv);
        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringContainsString("'@SUM(1)", $csv);
        $this->assertStringContainsString("'-2+3", $csv);

        $log = MemberAdminLog::sole();
        $this->assertSame([MemberAdminLog::LIST_EXPORTED, $this->admin->id, 'rows:3'], [$log->action, $log->actor_id, $log->detail]);
    }

    public function test_the_export_needs_a_post(): void
    {
        $this->actingAs($this->admin)->get('/admin/members/export')->assertMethodNotAllowed();
        $this->assertDatabaseCount('member_admin_log', 0);
    }
}
