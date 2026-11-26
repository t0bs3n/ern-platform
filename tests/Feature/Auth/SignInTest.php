<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SignInTest extends TestCase
{
    use RefreshDatabase;

    private const NEUTRAL = 'The e-mail address or the password is not right.';

    public function test_sign_in_page_shows_the_mockup_wording(): void
    {
        $this->get('/members/sign-in')
            ->assertOk()
            ->assertSee('Member area')
            ->assertSee('Sign in')
            ->assertSee('E-mail address')
            ->assertSee('Password')
            ->assertSee('Forgot your password?')
            ->assertSee('New member? Your welcome e-mail holds a link to set your password first.')
            ->assertSee('What members find here')
            ->assertSee('Apply online')
            ->assertSee('name="_token"', false)
            ->assertDontSee('remember');
    }

    public function test_an_approved_member_signs_in_with_the_right_password(): void
    {
        $user = User::factory()->create(['email' => 'member@example.test']);

        $this->post('/members/sign-in', ['email' => 'member@example.test', 'password' => 'password'])
            ->assertRedirect('/members');

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_wrong_password_is_refused_with_the_neutral_message(): void
    {
        User::factory()->create(['email' => 'member@example.test']);

        $this->from('/members/sign-in')
            ->post('/members/sign-in', ['email' => 'member@example.test', 'password' => 'wrong-password'])
            ->assertRedirect('/members/sign-in')
            ->assertSessionHasErrors(['email' => self::NEUTRAL]);

        $this->assertGuest();
    }

    public function test_an_unknown_address_gets_the_same_message_as_a_wrong_password(): void
    {
        $this->from('/members/sign-in')
            ->post('/members/sign-in', ['email' => 'nobody@example.test', 'password' => 'password'])
            ->assertSessionHasErrors(['email' => self::NEUTRAL]);

        $this->assertGuest();
    }

    public function test_a_user_who_is_not_approved_cannot_sign_in(): void
    {
        User::factory()->unapproved()->create(['email' => 'applicant@example.test']);

        $this->from('/members/sign-in')
            ->post('/members/sign-in', ['email' => 'applicant@example.test', 'password' => 'password'])
            ->assertSessionHasErrors(['email' => self::NEUTRAL]);

        $this->assertGuest();
    }

    public function test_a_user_without_a_password_cannot_sign_in(): void
    {
        User::factory()->withoutPassword()->create(['email' => 'new@example.test']);

        $this->from('/members/sign-in')
            ->post('/members/sign-in', ['email' => 'new@example.test', 'password' => ''])
            ->assertSessionHasErrors('password');
        $this->post('/members/sign-in', ['email' => 'new@example.test', 'password' => 'anything'])
            ->assertSessionHasErrors(['email' => self::NEUTRAL]);

        $this->assertGuest();
    }

    public function test_empty_fields_get_the_mockup_messages(): void
    {
        $this->from('/members/sign-in')
            ->post('/members/sign-in', ['email' => '', 'password' => ''])
            ->assertSessionHasErrors(['email' => 'Enter your e-mail address.', 'password' => 'Enter your password.']);
    }

    public function test_sign_in_locks_after_five_failures(): void
    {
        User::factory()->create(['email' => 'member@example.test']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/members/sign-in', ['email' => 'member@example.test', 'password' => 'wrong-password']);
        }

        // Even the right password is refused while the lock lasts.
        $response = $this->post('/members/sign-in', ['email' => 'member@example.test', 'password' => 'password']);

        $response->assertSessionHasErrors('email');
        $this->assertStringStartsWith('Too many attempts. Please wait', session('errors')->first('email'));
        $this->assertGuest();

        // After the wait, the right password works again.
        $this->travel(61)->seconds();
        $this->post('/members/sign-in', ['email' => 'member@example.test', 'password' => 'password'])
            ->assertRedirect('/members');
        $this->assertAuthenticated();
    }

    public function test_sign_in_never_sets_a_remember_me_cookie(): void
    {
        User::factory()->create(['email' => 'member@example.test']);

        $this->post('/members/sign-in', ['email' => 'member@example.test', 'password' => 'password', 'remember' => '1'])
            ->assertRedirect('/members')
            ->assertCookieMissing(Auth::guard()->getRecallerName());

        $this->assertNull(User::first()->remember_token);
    }

    public function test_the_email_address_is_matched_without_regard_to_capitals(): void
    {
        $user = User::factory()->create(['email' => 'Member@Example.test']);

        $this->assertSame('member@example.test', $user->fresh()->email);

        $this->post('/members/sign-in', ['email' => 'MEMBER@example.TEST', 'password' => 'password'])
            ->assertRedirect('/members');

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_member_signs_out_with_post_only(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/members/sign-out')->assertMethodNotAllowed();
        $this->assertAuthenticated();

        $this->actingAs($user)->post('/members/sign-out')
            ->assertRedirect('/members/sign-in')
            ->assertSessionHas('status', 'Signed out');

        $this->assertGuest();
    }

    public function test_a_signed_in_member_is_sent_away_from_the_sign_in_and_reset_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/members/sign-in')->assertRedirect('/members');
        $this->actingAs($user)->get('/members/forgot-password')->assertRedirect('/members');
        $this->actingAs($user)->get('/members/reset-password/some-token')->assertRedirect('/members');
    }

    public function test_expired_reset_links_are_cleared_every_day(): void
    {
        $events = collect(app(Schedule::class)->events())
            ->filter(fn ($event) => str_contains($event->command ?? '', 'auth:clear-resets'));

        $this->assertCount(1, $events);
        $this->assertSame('0 0 * * *', $events->first()->expression);
    }

    public function test_there_is_no_registration_and_no_fortify_default_address(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [])->assertNotFound();
        $this->get('/login')->assertNotFound();
        $this->get('/user/confirm-password')->assertNotFound();
        $this->get('/two-factor-challenge')->assertNotFound();
    }
}
