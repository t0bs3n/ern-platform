<?php

namespace Tests\Feature\Auth;

use App\Auth\Permissions;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GatesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Throw-away routes, one per gate. Nothing is added to routes/web.php.
        foreach (array_keys(Permissions::GATES) as $gate) {
            Route::middleware(['web', 'auth', 'can:'.$gate])->get('/_test/gate/'.$gate, fn () => 'ok');
        }
    }

    /** @return array<string, array{string, Role}> */
    public static function gateHolders(): array
    {
        $cases = [];
        foreach (Permissions::GATES as $gate => $roles) {
            foreach ($roles as $role) {
                $cases[$gate.' / '.$role->value] = [$gate, $role];
            }
        }

        return $cases;
    }

    /** @return array<string, array{string}> */
    public static function gates(): array
    {
        return array_combine(array_keys(Permissions::GATES), array_map(fn ($gate) => [$gate], array_keys(Permissions::GATES)));
    }

    #[DataProvider('gateHolders')]
    public function test_a_role_holder_passes_the_gate(string $gate, Role $role): void
    {
        $user = User::factory()->withRoles($role)->create();

        $this->actingAs($user)->get('/_test/gate/'.$gate)->assertOk();
    }

    #[DataProvider('gates')]
    public function test_admin_passes_every_gate(string $gate): void
    {
        $this->actingAs(User::factory()->withRoles(Role::Admin)->create())
            ->get('/_test/gate/'.$gate)->assertOk();
    }

    #[DataProvider('gates')]
    public function test_a_member_without_the_role_gets_403(string $gate): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/_test/gate/'.$gate)->assertForbidden();
    }

    #[DataProvider('gates')]
    public function test_a_guest_is_sent_to_sign_in(string $gate): void
    {
        $this->get('/_test/gate/'.$gate)->assertRedirect('/members/sign-in');
    }

    public function test_the_gate_matrix_matches_the_permissions(): void
    {
        foreach (Role::cases() as $role) {
            $user = User::factory()->withRoles($role)->create();

            foreach (Permissions::GATES as $gate => $roles) {
                $this->assertSame(
                    in_array($role, $roles, true),
                    Gate::forUser($user)->allows($gate),
                    "Role {$role->value} at gate {$gate}"
                );
            }
        }
    }

    public function test_the_permissions_match_the_agreed_table(): void
    {
        // Written out by hand, so that a mistake in Permissions::GATES makes this test fail.
        $agreed = [
            'manage-members' => ['admin'],
            'manage-conference' => ['conference_team', 'admin'],
            'review-submissions' => ['reviewer', 'admin'],
            'decide-submissions' => ['final_editor', 'admin'],
            'manage-database' => ['database_lead', 'admin'],
            'manage-teaching' => ['lecture_team', 'admin'],
            'edit-content' => ['editor', 'admin'],
            'administer' => ['admin'],
            'open-admin-area' => ['reviewer', 'final_editor', 'conference_team', 'database_lead', 'lecture_team', 'editor', 'admin'],
        ];

        $actual = array_map(fn (array $roles) => array_map(fn (Role $role) => $role->value, $roles), Permissions::GATES);

        $this->assertSame($agreed, $actual);
    }

    public function test_an_editor_does_not_pass_other_areas(): void
    {
        $editor = User::factory()->withRoles(Role::Editor)->create();

        $this->assertTrue(Gate::forUser($editor)->allows('edit-content'));
        $this->assertFalse(Gate::forUser($editor)->allows('manage-database'));
        $this->assertFalse(Gate::forUser($editor)->allows('manage-members'));
        $this->assertFalse(Gate::forUser($editor)->allows('administer'));
    }

    public function test_a_role_holder_who_is_not_approved_is_refused(): void
    {
        $user = User::factory()->withRoles(Role::Admin)->create();
        $user->forceFill(['approved_at' => null])->save();

        $this->actingAs($user)->get('/_test/gate/administer')->assertForbidden();
        $this->assertFalse(Gate::forUser($user->fresh())->allows('administer'));
    }

    public function test_a_user_holds_several_roles(): void
    {
        $user = User::factory()->withRoles(Role::ConferenceTeam, Role::FinalEditor)->create();

        $this->assertTrue($user->fresh()->hasRole(Role::Member));
        $this->assertTrue(Gate::forUser($user)->allows('manage-conference'));
        $this->assertTrue(Gate::forUser($user)->allows('decide-submissions'));
        $this->assertFalse(Gate::forUser($user)->allows('review-submissions'));
        $this->assertCount(3, $user->fresh()->roles);
    }

    public function test_a_role_is_not_stored_twice_for_one_user(): void
    {
        $user = User::factory()->create();
        $user->giveRole(Role::Member);
        $this->assertCount(1, $user->fresh()->roles);

        $this->expectException(QueryException::class);
        $user->roles()->create(['role' => Role::Member]);
    }

    public function test_roles_are_deleted_with_the_user(): void
    {
        $user = User::factory()->withRoles(Role::Admin)->create();
        $user->delete();

        $this->assertDatabaseCount('user_roles', 0);
    }

    public function test_role_labels_and_descriptions_match_the_mockup(): void
    {
        $this->assertSame('Final editor', Role::FinalEditor->label());
        $this->assertSame('Gives the final result on every submission. One person holds this role.', Role::FinalEditor->description());
        $this->assertSame('Database Lead', Role::DatabaseLead->label());
        $this->assertSame('Everything, including the rights of other members', Role::Admin->description());
        $this->assertSame('Sees the member area', Role::Member->description());
    }
}
