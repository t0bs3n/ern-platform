<?php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAreaMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'admin'])->get('/_test/admin', fn () => 'admin area');
    }

    /** @return array<string, array{Role}> */
    public static function rolesBeyondMember(): array
    {
        $cases = [];
        foreach (Role::cases() as $role) {
            if ($role !== Role::Member) {
                $cases[$role->value] = [$role];
            }
        }

        return $cases;
    }

    public function test_a_guest_is_sent_to_sign_in(): void
    {
        $this->get('/_test/admin')->assertRedirect('/members/sign-in');
    }

    public function test_a_member_only_gets_403_inside_the_site_frame(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/_test/admin')
            ->assertForbidden()
            ->assertSee('No access')
            ->assertSee('Your account does not have the right to open this page.')
            ->assertSee('class="site-header"', false)
            ->assertDontSee('Admin area');
    }

    #[DataProvider('rolesBeyondMember')]
    public function test_each_role_beyond_member_gets_in(Role $role): void
    {
        $this->actingAs(User::factory()->withRoles($role)->create())
            ->get('/_test/admin')
            ->assertOk()
            ->assertSee('admin area');
    }

    public function test_an_admin_who_is_not_approved_gets_403(): void
    {
        $user = User::factory()->withRoles(Role::Admin)->create();
        $user->forceFill(['approved_at' => null])->save();

        $this->actingAs($user)->get('/_test/admin')->assertForbidden();
    }
}
