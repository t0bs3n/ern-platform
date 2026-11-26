<?php

namespace Tests\Feature\Members;

use App\Enums\Role;
use App\Mail\ConfirmNewEmail;
use App\Mail\EmailChanged;
use App\Mail\MembershipEndRequested;
use App\Members\EmailChangeLink;
use App\Models\MembershipDecision;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutDefer();
        Mail::fake();
        $this->member = User::factory()->create([
            'first_name' => 'Maja', 'last_name' => 'Lindqvist', 'slug' => 'maja-lindqvist',
            'email' => 'maja@example.test', 'institution' => 'Nordic University (example)',
        ]);
    }

    /** @return array<string, mixed> */
    private function details(array $override = []): array
    {
        return $override + [
            'first_name' => 'Maja', 'last_name' => 'Lindqvist', 'institution' => 'Nordic University (example)',
            'country' => 'Sweden', 'career_stage' => 'PhD student', 'interests' => 'Esports psychology',
            'orcid' => '0000-0002-1825-0097', 'discord_name' => 'maja.l',
        ];
    }

    private function confirmLinkFromMail(): string
    {
        $url = null;
        Mail::assertSent(ConfirmNewEmail::class, function (ConfirmNewEmail $mail) use (&$url) {
            $url = $mail->url;

            return true;
        });

        return $url;
    }

    /** @return array<string, array{string, string}> */
    public static function routes(): array
    {
        return [
            'profile' => ['get', '/members/profile'],
            'save' => ['put', '/members/profile'],
            'email' => ['post', '/members/profile/email'],
            'end' => ['post', '/members/profile/end-membership'],
            'data' => ['get', '/members/profile/data'],
        ];
    }

    #[DataProvider('routes')]
    public function test_a_guest_is_sent_to_sign_in_and_an_unapproved_user_is_refused(string $method, string $url): void
    {
        $this->{$method}($url)->assertRedirect('/members/sign-in');

        $this->member->forceFill(['approved_at' => null])->save();
        $this->actingAs($this->member)->{$method}($url, $this->details(['first_name' => 'Changed']))->assertForbidden();
        $this->assertSame('Maja', $this->member->fresh()->first_name);
        Mail::assertNothingSent();
    }

    public function test_the_profile_shows_the_mockup_wording_and_the_own_details(): void
    {
        $this->actingAs($this->member)->get('/members/profile')->assertOk()
            ->assertSee('My details')
            ->assertSee('Keep your entry in the member list up to date yourself. No new application is needed.')
            ->assertSee('University or organisation')
            ->assertSee('ORCID iD')
            ->assertSee('Discord name')
            ->assertSee('Visibility')
            ->assertSee('Show me in the member directory')
            ->assertSee('I am open to collaboration requests')
            ->assertSee('Show my Discord name in the member directory')
            ->assertSee('Save changes')
            ->assertSee('value="Nordic University (example)"', false)
            ->assertSee('maja@example.test')
            ->assertDontSee('I am available as');
    }

    public function test_a_member_saves_details_and_choices(): void
    {
        $this->actingAs($this->member)->put('/members/profile', $this->details() + ['listed_in_directory' => '1', 'show_discord_in_directory' => '1'])
            ->assertRedirect('/members/profile')
            ->assertSessionHas('status', 'Your details are saved.');

        $member = $this->member->fresh();
        $this->assertSame('Sweden', $member->country);
        $this->assertSame('0000-0002-1825-0097', $member->orcid);
        $this->assertTrue($member->listed_in_directory);
        $this->assertFalse($member->open_to_collaboration);
        $this->assertTrue($member->show_discord_in_directory);

        // An unticked box is saved as "no".
        $this->actingAs($this->member)->put('/members/profile', $this->details());
        $this->assertFalse($this->member->fresh()->listed_in_directory);
    }

    public function test_wrong_input_is_refused(): void
    {
        $this->actingAs($this->member)->put('/members/profile', $this->details(['first_name' => '', 'orcid' => '1234', 'career_stage' => 'Wizard', 'interests' => str_repeat('a', 1001)]))
            ->assertSessionHasErrors([
                'first_name' => 'This field is needed.',
                'orcid' => 'Enter the ORCID iD as 16 digits in four groups, for example 0000-0002-1825-0097.',
                'career_stage' => 'Please choose one.',
                'interests' => 'This is too long.',
            ]);
    }

    public function test_a_member_changes_only_their_own_profile_and_nothing_protected(): void
    {
        $other = User::factory()->create(['first_name' => 'Other']);

        $this->actingAs($this->member)->put('/members/profile', $this->details() + [
            'id' => $other->id, 'user_id' => $other->id, 'email' => 'hacked@example.test',
            'approved_at' => null, 'slug' => 'sign-in', 'pending_email' => 'x@example.test', 'password' => 'new',
        ]);

        $this->assertSame('Other', $other->fresh()->first_name);
        $member = $this->member->fresh();
        $this->assertSame('maja@example.test', $member->email);
        $this->assertTrue($member->isApproved());
        $this->assertSame('maja-lindqvist', $member->slug);
        $this->assertNull($member->pending_email);
    }

    public function test_a_new_name_gets_a_new_address_that_avoids_reserved_ones(): void
    {
        $this->actingAs($this->member)->put('/members/profile', $this->details(['first_name' => 'Maja', 'last_name' => 'Berg']));
        $this->assertSame('maja-berg', $this->member->fresh()->slug);

        $this->actingAs($this->member)->put('/members/profile', $this->details(['first_name' => 'Sign', 'last_name' => 'In']));
        $this->assertSame('sign-in-2', $this->member->fresh()->slug);
    }

    public function test_changing_the_e_mail_needs_the_password_and_changes_nothing_until_confirmed(): void
    {
        $this->actingAs($this->member)->post('/members/profile/email', ['new_email' => 'maja.new@example.test', 'current_password' => 'wrong'])
            ->assertSessionHasErrors(['current_password' => 'The password is not right.']);
        Mail::assertNothingSent();

        $this->actingAs($this->member)->post('/members/profile/email', ['new_email' => 'Maja.New@example.test', 'current_password' => 'password'])
            ->assertSessionHas('status', 'Check the new address. If it can be used, a link to confirm it is on its way. Your address stays as it is until you click the link.');

        $this->assertSame('maja@example.test', $this->member->fresh()->email);
        $this->assertSame('maja.new@example.test', $this->member->fresh()->pending_email);
        Mail::assertSent(ConfirmNewEmail::class, fn ($mail) => $mail->hasTo('maja.new@example.test') && count($mail->to) === 1);
    }

    public function test_confirming_changes_the_address_and_tells_the_old_one_without_naming_the_new(): void
    {
        DB::table('password_reset_tokens')->insert(['email' => 'maja@example.test', 'token' => 'x', 'created_at' => now()]);
        $this->actingAs($this->member)->post('/members/profile/email', ['new_email' => 'maja.new@example.test', 'current_password' => 'password']);
        $link = $this->confirmLinkFromMail();

        $this->actingAs($this->member)->get($link)
            ->assertRedirect('/members/profile')
            ->assertSessionHas('status', 'Your new e-mail address is saved. Use it to sign in from now on.');

        $member = $this->member->fresh();
        $this->assertSame('maja.new@example.test', $member->email);
        $this->assertNull($member->pending_email);
        $this->assertNotNull($member->email_verified_at);
        $this->assertDatabaseCount('password_reset_tokens', 0);

        Mail::assertSent(EmailChanged::class, function (EmailChanged $mail) {
            return $mail->hasTo('maja@example.test') && ! str_contains($mail->render(), 'maja.new@example.test');
        });

        // The same link does not work twice.
        $this->actingAs($member)->get($link)->assertForbidden()->assertSee('This link is not valid or has expired.');
    }

    public function test_bad_confirm_links_are_refused(): void
    {
        $this->actingAs($this->member)->post('/members/profile/email', ['new_email' => 'first@example.test', 'current_password' => 'password']);
        $first = $this->confirmLinkFromMail();
        Mail::fake();
        $this->actingAs($this->member)->post('/members/profile/email', ['new_email' => 'second@example.test', 'current_password' => 'password']);

        // A replaced request, a changed link, another member's session, an expired link.
        $this->actingAs($this->member)->get($first)->assertForbidden();
        $second = $this->confirmLinkFromMail();
        $this->actingAs($this->member)->get($second.'x')->assertForbidden();
        $this->actingAs(User::factory()->create())->get($second)->assertForbidden();
        $this->travel(25)->hours();
        $this->actingAs($this->member)->get($second)->assertForbidden();

        $this->assertSame('maja@example.test', $this->member->fresh()->email);
    }

    public function test_a_taken_address_gets_the_same_answer_and_no_e_mail(): void
    {
        User::factory()->create(['email' => 'taken@example.test']);

        $this->actingAs($this->member)->post('/members/profile/email', ['new_email' => 'taken@example.test', 'current_password' => 'password'])
            ->assertSessionHas('status', 'Check the new address. If it can be used, a link to confirm it is on its way. Your address stays as it is until you click the link.');

        Mail::assertNothingSent();

        // The profile page looks the same as for a free address.
        $this->assertSame('taken@example.test', $this->member->fresh()->pending_email);
        $strip = fn (string $html) => preg_replace(['/name="_token" value="[^"]*"/', '/Waiting for confirmation since [^<]*/'], '', $html);
        $takenPage = $strip($this->actingAs($this->member)->get('/members/profile')->getContent());

        $this->actingAs($this->member)->post('/members/profile/email', ['new_email' => 'free@example.test', 'current_password' => 'password']);
        $freePage = $strip($this->actingAs($this->member)->get('/members/profile')->getContent());

        $this->assertSame($takenPage, $freePage);
    }

    public function test_asking_for_the_own_address_sends_nothing(): void
    {
        $this->actingAs($this->member)->post('/members/profile/email', ['new_email' => 'maja@example.test', 'current_password' => 'password'])
            ->assertSessionHas('status');

        Mail::assertNothingSent();
        $this->assertSame('maja@example.test', $this->member->fresh()->email);
    }

    public function test_confirming_renews_the_session(): void
    {
        $this->actingAs($this->member)->post('/members/profile/email', ['new_email' => 'maja.new@example.test', 'current_password' => 'password']);
        $link = $this->confirmLinkFromMail();
        $before = session()->getId();

        $this->actingAs($this->member)->get($link);

        $this->assertNotSame($before, session()->getId());
    }

    public function test_the_confirm_link_needs_the_right_signed_in_member(): void
    {
        $this->actingAs($this->member)->post('/members/profile/email', ['new_email' => 'maja.new@example.test', 'current_password' => 'password']);
        $link = $this->confirmLinkFromMail();

        // A guest is sent to sign-in, an unapproved member is refused.
        auth()->logout();
        $this->get($link)->assertRedirect('/members/sign-in');
        $this->member->forceFill(['approved_at' => null])->save();
        $this->actingAs($this->member)->get($link)->assertForbidden();
        $this->member->forceFill(['approved_at' => now()])->save();

        // A correctly signed link for another member is refused for this member.
        $other = User::factory()->create();
        $other->forceFill(['pending_email' => 'other.new@example.test', 'pending_email_requested_at' => now()])->save();
        $this->actingAs($this->member)->get(EmailChangeLink::for($other))->assertForbidden();

        $this->assertSame('maja@example.test', $this->member->fresh()->email);
        $this->assertSame($other->email, $other->fresh()->email);
    }

    public function test_an_address_taken_before_confirming_is_refused(): void
    {
        $this->actingAs($this->member)->post('/members/profile/email', ['new_email' => 'race@example.test', 'current_password' => 'password']);
        $link = $this->confirmLinkFromMail();
        User::factory()->create(['email' => 'race@example.test']);

        $this->actingAs($this->member)->get($link)->assertSessionHas('status', 'This address cannot be used. Your e-mail address stays as it was.');
        $this->assertSame('maja@example.test', $this->member->fresh()->email);
    }

    public function test_a_failing_confirmation_e_mail_clears_the_request_and_logs_no_address(): void
    {
        Log::spy();
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('550 <new@example.test> rejected'));

        $this->actingAs($this->member)->post('/members/profile/email', ['new_email' => 'new@example.test', 'current_password' => 'password'])
            ->assertSessionHas('status', 'Check the new address. If it can be used, a link to confirm it is on its way. Your address stays as it is until you click the link.');

        $this->assertNull($this->member->fresh()->pending_email);
        Log::shouldHaveReceived('warning')->withArgs(fn ($message, $context) => ! str_contains(json_encode($context), 'example.test'));
    }

    public function test_the_nightly_job_forgets_unconfirmed_addresses_and_is_scheduled(): void
    {
        $this->member->forceFill(['pending_email' => 'old@example.test', 'pending_email_requested_at' => now()->subHours(25)])->save();
        $fresh = User::factory()->create();
        $fresh->forceFill(['pending_email' => 'fresh@example.test', 'pending_email_requested_at' => now()->subHour()])->save();

        $this->artisan('ern:clear-expired-email-changes')->assertSuccessful();

        $this->assertNull($this->member->fresh()->pending_email);
        $this->assertSame('fresh@example.test', $fresh->fresh()->pending_email);
        $this->assertTrue(collect(app(Schedule::class)->events())->contains(fn ($e) => str_contains($e->command ?? '', 'ern:clear-expired-email-changes')));
    }

    public function test_ending_the_membership_writes_to_each_admin_and_deletes_nothing(): void
    {
        $admins = User::factory()->count(2)->withRoles(Role::Admin)->create();
        User::factory()->withRoles(Role::Admin)->unapproved()->create();

        $this->actingAs($this->member)->post('/members/profile/end-membership')->assertSessionHasErrors(['confirm' => 'Please tick the box to confirm.']);
        Mail::assertNothingSent();

        $this->actingAs($this->member)->post('/members/profile/end-membership', ['confirm' => '1'])
            ->assertSessionHas('status', 'Your request is sent. The board will contact you. Nothing is deleted until then.');

        Mail::assertSentCount(2);
        foreach ($admins as $admin) {
            Mail::assertSent(MembershipEndRequested::class, fn ($mail) => $mail->hasTo($admin->email) && count($mail->to) === 1);
        }

        $member = $this->member->fresh();
        $this->assertNotNull($member);
        $this->assertTrue($member->isApproved());
        $this->assertTrue($member->hasRole(Role::Member));
        $this->assertNotNull($member->membership_end_requested_at);

        // Once a day at most.
        $this->actingAs($this->member)->post('/members/profile/end-membership', ['confirm' => '1']);
        Mail::assertSentCount(2);
    }

    public function test_the_end_request_e_mail_holds_no_profile_details(): void
    {
        $this->member->update(['interests' => 'Secret interests', 'discord_name' => 'secret.discord']);
        $html = (new MembershipEndRequested($this->member->fresh()))->render();

        $this->assertStringContainsString('Maja Lindqvist', $html);
        $this->assertStringContainsString('maja@example.test', $html);
        $this->assertStringNotContainsString('Secret interests', $html);
        $this->assertStringNotContainsString('secret.discord', $html);
    }

    public function test_a_failing_end_request_says_so_and_sets_nothing(): void
    {
        User::factory()->withRoles(Role::Admin)->create();
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('down'));

        $this->actingAs($this->member)->post('/members/profile/end-membership', ['confirm' => '1'])
            ->assertSessionHas('status', 'Your request could not be sent. Please try again later, or write to board@esportsresearch.net.');

        $this->assertNull($this->member->fresh()->membership_end_requested_at);
    }

    public function test_the_download_holds_the_own_data_only_as_a_json_file(): void
    {
        $other = User::factory()->create(['first_name' => 'Somebody', 'email' => 'other@example.test']);
        $decision = new MembershipDecision;
        $decision->forceFill(['application_id' => 1, 'decision' => 'approved', 'decided_by' => $other->id, 'member_id' => $this->member->id, 'applied_at' => '2026-11-01', 'agreed_at' => '2026-11-01 10:00:00'])->save();

        $response = $this->actingAs($this->member)->get('/members/profile/data')->assertOk();

        $this->assertStringContainsString('attachment; filename="ern-my-data-', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $data = $response->json();
        $this->assertSame('maja@example.test', $data['email']);
        $this->assertSame('Nordic University (example)', $data['institution']);
        $this->assertSame(['Member'], $data['roles']);
        $this->assertSame('2026-11-01', $data['application']['applied_on']);

        $raw = $response->getContent();
        foreach (['password', 'remember_token', 'Somebody', 'other@example.test', '"id"', 'decided_by'] as $never) {
            $this->assertStringNotContainsString($never, $raw);
        }
    }
}
