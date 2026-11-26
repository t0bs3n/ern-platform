<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberAreaAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_is_sent_to_sign_in(): void
    {
        $this->get('/members')->assertRedirect('/members/sign-in');
    }

    public function test_a_signed_in_member_sees_the_member_area(): void
    {
        $user = User::factory()->create(['first_name' => 'Ada']);

        $this->actingAs($user)->get('/members')
            ->assertOk()
            ->assertSee('Welcome back, Ada')
            ->assertSee('Sign out');
    }

    public function test_new_members_are_not_listed_in_the_directory_by_default(): void
    {
        $user = User::create([
            'first_name' => 'Test', 'last_name' => 'Person', 'slug' => 'test-person',
            'email' => 'test@example.test', 'password' => 'a-password',
        ]);

        $this->assertFalse($user->fresh()->listed_in_directory);
        $this->assertNull($user->fresh()->approved_at);
    }

    public function test_approval_cannot_be_set_by_mass_assignment(): void
    {
        $user = User::create([
            'first_name' => 'Test', 'last_name' => 'Person', 'slug' => 'test-person',
            'email' => 'test@example.test', 'password' => 'a-password', 'approved_at' => now(),
        ]);

        $this->assertNull($user->fresh()->approved_at);
    }
}
