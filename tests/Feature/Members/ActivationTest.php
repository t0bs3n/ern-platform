<?php

namespace Tests\Feature\Members;

use App\Mail\PasswordResetLink;
use App\Mail\WelcomeMember;
use App\Members\ActivateMember;
use App\Members\ActivationLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ActivationTest extends TestCase
{
    use RefreshDatabase;

    private User $member;

    private string $link;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->member = User::factory()->withoutPassword()->unverified()->create(['email' => 'noor@example.test', 'first_name' => 'Noor']);
        $this->link = ActivationLink::for($this->member);
    }

    /** @return array<string, string> */
    private function password(string $one = 'a-good-password', ?string $two = null): array
    {
        return ['password' => $one, 'password_confirmation' => $two ?? $one];
    }

    /** The query of the link, for the "Send a new link" form. */
    private function signatureOf(string $link): array
    {
        parse_str(parse_url($link, PHP_URL_QUERY), $query);

        return ['expires' => $query['expires'], 'signature' => $query['signature']];
    }

    public function test_a_valid_link_shows_the_form_with_the_mockup_wording(): void
    {
        $this->get($this->link)->assertOk()
            ->assertSee('Welcome to ERN')
            ->assertSee('The board has confirmed your membership. Choose a password to open the member area.')
            ->assertSee('New password')
            ->assertSee('At least eight characters.')
            ->assertSee('Repeat the password')
            ->assertSee('Set password and sign in')
            ->assertSee('I already have a password')
            ->assertSee('name="_token"', false)
            ->assertSee('action="'.e($this->link).'"', false);
    }

    public function test_setting_a_password_signs_in_and_lands_in_the_member_area(): void
    {
        $this->post($this->link, $this->password())
            ->assertRedirect('/members')
            ->assertSessionHas('status', 'Your account is active. Welcome to the member area.');

        $this->assertAuthenticatedAs($this->member);
        $this->get('/members')->assertSee('Your account is active. Welcome to the member area.');
    }

    public function test_the_password_is_stored_hashed_and_the_address_counts_as_confirmed(): void
    {
        $this->post($this->link, $this->password());

        $member = $this->member->fresh();
        $this->assertNotSame('a-good-password', $member->password);
        $this->assertTrue(Hash::check('a-good-password', $member->password));
        $this->assertNotNull($member->email_verified_at);
    }

    public function test_activation_regenerates_the_session(): void
    {
        $this->get($this->link);
        $before = session()->getId();

        $this->post($this->link, $this->password());

        $this->assertNotSame($before, session()->getId());
    }

    public function test_weak_and_differing_passwords_are_refused_with_the_mockup_messages(): void
    {
        $this->post($this->link, $this->password('short'))
            ->assertRedirect($this->link)
            ->assertSessionHasErrors(['password' => 'Choose a password of at least eight characters.']);
        $this->post($this->link, $this->password('a-good-password', 'another-password'))
            ->assertSessionHasErrors(['password' => 'The two passwords differ.']);

        $this->assertNull($this->member->fresh()->password);
        $this->assertGuest();
    }

    public function test_an_expired_link_shows_the_friendly_page_and_cannot_set_a_password(): void
    {
        $this->travel(8)->days();

        $this->get($this->link)->assertStatus(410)
            ->assertSee('This link has expired')
            ->assertSee('Send a new link')
            ->assertDontSee('Set password and sign in');
        $this->post($this->link, $this->password())->assertStatus(410);

        $this->assertNull($this->member->fresh()->password);
    }

    public function test_a_form_sent_after_expiry_is_refused(): void
    {
        $this->travel(6)->days();
        $this->get($this->link)->assertOk();
        $this->travel(2)->days();

        $this->post($this->link, $this->password())->assertStatus(410);
        $this->assertGuest();
    }

    public function test_a_used_link_shows_the_used_page_and_changes_nothing(): void
    {
        $this->post($this->link, $this->password());
        auth()->logout();
        $hash = $this->member->fresh()->password;

        $this->get($this->link)->assertStatus(410)->assertSee('This link was already used')->assertSee('Forgot your password?');
        $this->post($this->link, $this->password('a-different-password'))->assertStatus(410);

        $this->assertSame($hash, $this->member->fresh()->password);
    }

    public function test_broken_links_show_the_not_valid_page(): void
    {
        $other = User::factory()->withoutPassword()->create();
        $unapproved = User::factory()->withoutPassword()->unapproved()->create();

        foreach ([
            $this->link.'0',
            str_replace('/activate/'.$this->member->id.'?', '/activate/'.$other->id.'?', $this->link),
            '/members/activate/'.$this->member->id,
            ActivationLink::for($unapproved),
        ] as $broken) {
            $this->get($broken)->assertForbidden()->assertSee('This link is not valid')->assertDontSee('Send a new link');
            $this->post($broken, $this->password())->assertForbidden();
        }

        $deletedLink = ActivationLink::for($other);
        $other->delete();
        $this->get($deletedLink)->assertForbidden()->assertSee('This link is not valid');

        $this->assertNull($this->member->fresh()->password);
        $this->assertNull($unapproved->fresh()->password);
    }

    public function test_a_signed_in_member_is_sent_away_from_the_link(): void
    {
        $this->actingAs(User::factory()->create())->get($this->link)->assertRedirect('/members');
        $this->actingAs(User::factory()->create())->post($this->link, $this->password())->assertRedirect('/members');

        $this->assertNull($this->member->fresh()->password);
    }

    public function test_a_new_link_is_sent_to_the_member_only_and_works(): void
    {
        $this->travel(8)->days();

        $this->post(route('members.activate.resend', $this->member), $this->signatureOf($this->link))
            ->assertOk()
            ->assertSee('If your membership still waits for a password, a new link is on its way.');

        $newLink = null;
        Mail::assertSent(WelcomeMember::class, function (WelcomeMember $mail) use (&$newLink) {
            $newLink = $mail->url;

            return $mail->hasTo('noor@example.test') && count($mail->to) === 1;
        });

        $this->get($this->link)->assertStatus(410);
        $this->post($newLink, $this->password())->assertRedirect('/members');
        $this->assertAuthenticatedAs($this->member);
    }

    public function test_no_new_link_once_a_password_is_set_or_for_an_unapproved_user_and_the_answer_is_the_same(): void
    {
        $this->post($this->link, $this->password());
        auth()->logout();
        $this->flushSession();
        $this->travel(8)->days();

        $answer = $this->post(route('members.activate.resend', $this->member), $this->signatureOf($this->link))->assertOk()->getContent();

        $other = User::factory()->withoutPassword()->create();
        $otherLink = ActivationLink::for($other);
        $other->forceFill(['approved_at' => null])->save();
        $second = $this->post(route('members.activate.resend', $other), $this->signatureOf($otherLink))->assertOk()->getContent();

        Mail::assertNothingSent();
        $strip = fn (string $html) => preg_replace('/name="_token" value="[^"]*"/', '', $html);
        $this->assertSame($strip($answer), $strip($second));
    }

    public function test_a_new_link_needs_a_correctly_signed_link(): void
    {
        $this->post(route('members.activate.resend', $this->member), ['expires' => time() - 10, 'signature' => 'made-up'])
            ->assertForbidden()
            ->assertSee('This link is not valid');

        $other = User::factory()->withoutPassword()->create();
        $this->post(route('members.activate.resend', $other), $this->signatureOf($this->link))->assertForbidden();

        Mail::assertNothingSent();
    }

    public function test_new_link_requests_are_limited_and_the_answer_stays_the_same(): void
    {
        $this->travel(8)->days();

        foreach (range(1, 5) as $n) {
            $this->post(route('members.activate.resend', $this->member), $this->signatureOf($this->link))
                ->assertOk()
                ->assertSee('Check your e-mail');
        }

        Mail::assertSentCount(3);
    }

    public function test_the_action_refuses_a_member_who_has_a_password_already(): void
    {
        $activate = app(ActivateMember::class);

        $this->assertNotNull($activate($this->member->id, 'a-good-password'));
        $hash = $this->member->fresh()->password;

        $this->assertNull($activate($this->member->id, 'another-password'));
        $this->assertSame($hash, $this->member->fresh()->password);
    }

    public function test_a_withdrawn_approval_between_opening_and_sending_shows_not_valid(): void
    {
        $this->get($this->link)->assertOk();
        $this->member->forceFill(['approved_at' => null])->save();

        $this->post($this->link, $this->password())->assertForbidden()->assertSee('This link is not valid');
        $this->assertGuest();
    }

    public function test_new_link_requests_from_one_address_are_limited_across_members(): void
    {
        $links = collect(range(1, 6))->map(function () {
            $member = User::factory()->withoutPassword()->create();

            return [$member, ActivationLink::for($member)];
        });
        $this->travel(8)->days();

        foreach ($links as [$member, $link]) {
            $this->travel(61)->seconds(); // stay under the per-minute limit of the page itself
            foreach (range(1, 2) as $n) {
                $this->post(route('members.activate.resend', $member), $this->signatureOf($link))->assertOk();
            }
        }

        Mail::assertSentCount(10);
    }

    public function test_forgot_password_still_works_for_a_member_without_a_password(): void
    {
        $this->post('/members/forgot-password', ['email' => 'noor@example.test']);

        Mail::assertSent(PasswordResetLink::class, fn ($mail) => $mail->hasTo('noor@example.test'));
    }
}
