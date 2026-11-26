<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordResetLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_forgot_page_shows_the_mockup_wording(): void
    {
        $this->get('/members/forgot-password')
            ->assertOk()
            ->assertSee('Set a new password')
            ->assertSee('Enter the e-mail address of your membership. We send you a link.')
            ->assertSee('Send the link')
            ->assertSee('Back to sign in');
    }

    public function test_a_reset_link_is_mailed_to_a_known_address(): void
    {
        Mail::fake();
        $user = User::factory()->create(['email' => 'member@example.test']);

        $this->post('/members/forgot-password', ['email' => 'member@example.test'])
            ->assertRedirect('/members/forgot-password');

        Mail::assertSent(PasswordResetLink::class, fn (PasswordResetLink $mail) => $mail->hasTo('member@example.test') && $mail->user->is($user));
        $this->get('/members/forgot-password')
            ->assertSee('Check your e-mail')
            ->assertSee('If this address belongs to a member, a link to set a new password is on its way.');
    }

    public function test_an_unknown_address_gets_the_identical_answer_and_no_mail(): void
    {
        Mail::fake();
        User::factory()->create(['email' => 'member@example.test']);

        $known = $this->post('/members/forgot-password', ['email' => 'member@example.test']);
        $knownPage = $this->get('/members/forgot-password')->getContent();

        Mail::fake();
        $unknown = $this->post('/members/forgot-password', ['email' => 'nobody@example.test']);
        $unknownPage = $this->get('/members/forgot-password')->getContent();

        $this->assertSame($known->getStatusCode(), $unknown->getStatusCode());
        $this->assertSame($known->headers->get('Location'), $unknown->headers->get('Location'));
        $unknown->assertSessionHasNoErrors();
        $this->assertSame($this->stripCsrfToken($knownPage), $this->stripCsrfToken($unknownPage));
        Mail::assertNothingSent();
    }

    public function test_a_throttled_repeat_request_gets_the_identical_answer(): void
    {
        Mail::fake();
        User::factory()->create(['email' => 'member@example.test']);

        $this->post('/members/forgot-password', ['email' => 'member@example.test']);
        $this->post('/members/forgot-password', ['email' => 'member@example.test'])
            ->assertRedirect('/members/forgot-password')
            ->assertSessionHasNoErrors()
            ->assertSessionHas('reset_link_requested', true);

        Mail::assertSentCount(1);
    }

    public function test_an_unapproved_applicant_gets_the_same_answer_and_no_mail(): void
    {
        Mail::fake();
        User::factory()->unapproved()->create(['email' => 'applicant@example.test']);

        $this->post('/members/forgot-password', ['email' => 'applicant@example.test'])
            ->assertRedirect('/members/forgot-password')
            ->assertSessionHasNoErrors()
            ->assertSessionHas('reset_link_requested', true);

        Mail::assertNothingSent();
    }

    public function test_an_invalid_address_is_refused_with_the_mockup_message(): void
    {
        $this->from('/members/forgot-password')
            ->post('/members/forgot-password', ['email' => 'not an address'])
            ->assertSessionHasErrors(['email' => 'Enter your e-mail address.']);
    }

    public function test_the_link_sets_a_new_password_and_the_old_one_stops_working(): void
    {
        $user = User::factory()->create(['email' => 'member@example.test']);
        $token = Password::createToken($user);

        $this->get('/members/reset-password/'.$token.'?email=member@example.test')
            ->assertOk()
            ->assertSee('New password')
            ->assertSee('At least eight characters.')
            ->assertSee('Repeat the password');

        $this->post('/members/reset-password', [
            'token' => $token,
            'email' => 'member@example.test',
            'password' => 'a-new-password',
            'password_confirmation' => 'a-new-password',
        ])->assertRedirect('/members/sign-in')
            ->assertSessionHas('status', 'Your new password is saved. Sign in with it.');

        $this->assertTrue(Hash::check('a-new-password', $user->fresh()->password));
        $this->assertGuest();

        $this->post('/members/sign-in', ['email' => 'member@example.test', 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->post('/members/sign-in', ['email' => 'member@example.test', 'password' => 'a-new-password'])
            ->assertRedirect('/members');
    }

    public function test_a_short_password_and_a_mismatch_are_refused_with_the_mockup_wording(): void
    {
        $user = User::factory()->create(['email' => 'member@example.test']);
        $token = Password::createToken($user);

        $this->post('/members/reset-password', [
            'token' => $token, 'email' => 'member@example.test',
            'password' => 'short', 'password_confirmation' => 'short',
        ])->assertSessionHasErrors(['password' => 'Choose a password of at least eight characters.']);

        $this->post('/members/reset-password', [
            'token' => $token, 'email' => 'member@example.test',
            'password' => 'long-enough-1', 'password_confirmation' => 'long-enough-2',
        ])->assertSessionHasErrors(['password' => 'The two passwords differ.']);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_an_invalid_or_expired_token_is_refused(): void
    {
        $user = User::factory()->create(['email' => 'member@example.test']);

        $this->post('/members/reset-password', [
            'token' => 'not-a-real-token', 'email' => 'member@example.test',
            'password' => 'a-new-password', 'password_confirmation' => 'a-new-password',
        ])->assertSessionHasErrors(['email' => 'This link has expired or was already used. Ask for a new one.']);

        $token = Password::createToken($user);
        $this->travel(61)->minutes();

        $this->post('/members/reset-password', [
            'token' => $token, 'email' => 'member@example.test',
            'password' => 'a-new-password', 'password_confirmation' => 'a-new-password',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    private function stripCsrfToken(string $html): string
    {
        return preg_replace('/name="_token" value="[^"]*"/', '', $html);
    }

    public function test_reset_requests_are_limited_per_address_asked_for_and_per_internet_address(): void
    {
        Mail::fake();
        User::factory()->create(['email' => 'member@example.test']);

        for ($i = 0; $i < 3; $i++) {
            $this->travel(2)->minutes();
            $this->post('/members/forgot-password', ['email' => 'member@example.test'])->assertRedirect();
        }
        $this->travel(2)->minutes();
        $this->post('/members/forgot-password', ['email' => 'member@example.test'])->assertTooManyRequests();
        Mail::assertSentCount(3);

        // The block on one address lasts an hour, not a day.
        $this->travel(1)->hours();
        $this->post('/members/forgot-password', ['email' => 'member@example.test'])->assertRedirect();
        Mail::assertSentCount(4);

        // Ten requests an hour from one internet address, whatever addresses they name.
        $this->travel(2)->hours();
        for ($i = 0; $i < 10; $i++) {
            $this->post('/members/forgot-password', ['email' => 'other'.$i.'@example.test'])->assertRedirect();
        }
        $this->post('/members/forgot-password', ['email' => 'other10@example.test'])->assertTooManyRequests();
    }
}
