<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Mail\ApplicationDeclined;
use App\Mail\WelcomeMember;
use App\Models\MembershipApplication;
use App\Models\MembershipDecision;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MembersApplicationsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private MembershipApplication $application;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->admin = User::factory()->withRoles(Role::Admin)->create();
        $this->application = MembershipApplication::factory()->create([
            'first_name' => 'Tomás', 'last_name' => 'Herrera', 'email' => 'tomas@example.test',
            'institution' => 'Example Institute (example)', 'country' => 'Chile', 'city' => 'Santiago',
            'career_stage' => 'Researcher with a PhD', 'interests' => 'Esports economics', 'discord_name' => 'tomas.h',
        ]);
    }

    private function approveUrl(?MembershipApplication $application = null): string
    {
        return '/admin/members/applications/'.($application ?? $this->application)->id.'/approve';
    }

    private function declineUrl(?MembershipApplication $application = null): string
    {
        return '/admin/members/applications/'.($application ?? $this->application)->id.'/decline';
    }

    public function test_an_admin_sees_the_open_applications_with_the_mockup_wording(): void
    {
        $this->actingAs($this->admin)->get('/admin/members')->assertOk()
            ->assertSee('Applications to confirm')
            ->assertSee('Applied on '.$this->application->created_at->format('j F Y'))
            ->assertSee('Tomás Herrera')
            ->assertSee('Researcher with a PhD · Example Institute (example) · Chile')
            ->assertSee('Approve')
            ->assertSee('Decline')
            ->assertSee('name="_token"', false);
    }

    public function test_an_empty_list_says_so(): void
    {
        $this->application->delete();

        $this->actingAs($this->admin)->get('/admin/members')->assertSee('No applications wait for the board.');
    }

    /** @return array<string, array{string, string}> */
    public static function addresses(): array
    {
        return [
            'tab' => ['get', '/admin/members'],
            'approve' => ['post', 'approve'],
            'decline' => ['post', 'decline'],
        ];
    }

    private function resolve(string $url): string
    {
        return match ($url) {
            'approve' => $this->approveUrl(),
            'decline' => $this->declineUrl(),
            default => $url,
        };
    }

    #[DataProvider('addresses')]
    public function test_a_guest_is_sent_to_sign_in(string $method, string $url): void
    {
        $this->{$method}($this->resolve($url))->assertRedirect('/members/sign-in');
        $this->assertSame(MembershipApplication::OPEN, $this->application->fresh()->status);
    }

    #[DataProvider('addresses')]
    public function test_members_editors_and_the_conference_team_are_refused(string $method, string $url): void
    {
        foreach ([User::factory()->create(), User::factory()->withRoles(Role::Editor)->create(), User::factory()->withRoles(Role::ConferenceTeam)->create()] as $user) {
            $this->actingAs($user)->{$method}($this->resolve($url))->assertForbidden();
        }

        $this->assertSame(MembershipApplication::OPEN, $this->application->fresh()->status);
        $this->assertSame(0, User::where('email', 'tomas@example.test')->count());
        Mail::assertNothingSent();
    }

    public function test_an_unapproved_admin_is_refused(): void
    {
        $this->admin->forceFill(['approved_at' => null])->save();

        $this->actingAs($this->admin)->post($this->approveUrl())->assertForbidden();
        $this->assertDatabaseCount('membership_decisions', 0);
    }

    public function test_approval_creates_an_approved_member_with_the_member_role(): void
    {
        $this->actingAs($this->admin)->from('/admin/members')->post($this->approveUrl())
            ->assertRedirect('/admin/members')
            ->assertSessionHas('status', 'Approved, welcome e-mail sent');

        $member = User::where('email', 'tomas@example.test')->sole();
        $this->assertTrue($member->isApproved());
        $this->assertTrue($member->hasRole(Role::Member));
        $this->assertSame(['member'], $member->roles->map(fn ($r) => $r->role->value)->all());
        $this->assertSame('tomas-herrera', $member->slug);
        $this->assertSame('Tomás', $member->first_name);
        $this->assertSame('Esports economics', $member->interests);
        $this->assertSame('tomas.h', $member->discord_name);
        $this->assertSame('Researcher with a PhD', $member->career_stage);
        $this->assertNull($member->password);
        $this->assertFalse($member->listed_in_directory);
    }

    public function test_approval_deletes_the_application_and_logs_without_personal_data(): void
    {
        $this->actingAs($this->admin)->post($this->approveUrl());

        $this->assertDatabaseCount('membership_applications', 0);
        $this->assertDatabaseMissing('membership_applications', ['city' => 'Santiago']);

        $decision = MembershipDecision::sole();
        $this->assertSame(MembershipDecision::APPROVED, $decision->decision);
        $this->assertSame($this->admin->id, $decision->decided_by);
        $this->assertSame(User::where('email', 'tomas@example.test')->value('id'), $decision->member_id);
        $this->assertNotNull($decision->agreed_at);
        $this->assertNotNull($decision->created_at);

        $row = json_encode($decision->getAttributes());
        foreach (['Tomás', 'Herrera', 'tomas@example.test', 'Chile', 'Santiago'] as $personal) {
            $this->assertStringNotContainsString($personal, $row);
        }
    }

    public function test_the_welcome_e_mail_goes_to_the_applicant_only(): void
    {
        $this->actingAs($this->admin)->post($this->approveUrl());

        Mail::assertSent(WelcomeMember::class, fn (WelcomeMember $mail) => $mail->hasTo('tomas@example.test') && count($mail->to) === 1 && $mail->cc === [] && $mail->bcc === []);
        Mail::assertSentCount(1);
        $this->assertNotNull(MembershipDecision::sole()->email_sent_at);
    }

    public function test_approval_is_refused_when_the_address_already_belongs_to_a_user(): void
    {
        User::factory()->create(['email' => 'tomas@example.test']);

        $this->actingAs($this->admin)->post($this->approveUrl())
            ->assertSessionHas('status', 'This e-mail address already belongs to a member. The application stays open.');

        $this->assertSame(MembershipApplication::OPEN, $this->application->fresh()->status);
        $this->assertDatabaseCount('membership_decisions', 0);
        Mail::assertNothingSent();
    }

    public function test_deciding_twice_sends_one_e_mail_and_creates_one_member(): void
    {
        $this->actingAs($this->admin)->post($this->approveUrl());
        $this->actingAs($this->admin)->post($this->approveUrl())->assertNotFound();

        $declined = MembershipApplication::factory()->create();
        $this->actingAs($this->admin)->post($this->declineUrl($declined));
        $this->actingAs($this->admin)->post($this->approveUrl($declined))->assertSessionHas('status', 'This application was already decided.');
        $this->actingAs($this->admin)->post($this->declineUrl($declined))->assertSessionHas('status', 'This application was already decided.');

        $this->assertSame(1, User::where('email', 'tomas@example.test')->count());
        Mail::assertSentCount(2);
        $this->assertDatabaseCount('membership_decisions', 2);
    }

    public function test_a_failing_welcome_e_mail_keeps_the_approval_and_says_so(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP is down'));

        $this->actingAs($this->admin)->post($this->approveUrl())
            ->assertSessionHas('status', 'Approved. The welcome e-mail could not be sent. The new member can use "Forgot your password?" on the sign-in page.');

        $this->assertTrue(User::where('email', 'tomas@example.test')->sole()->isApproved());
        $this->assertNull(MembershipDecision::sole()->email_sent_at);

        $this->actingAs($this->admin)->get('/admin/members')->assertSee('Approved, welcome e-mail not sent');
    }

    public function test_a_failing_decline_e_mail_keeps_the_decision_and_says_so(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('550 <tomas@example.test> rejected'));

        $this->actingAs($this->admin)->post($this->declineUrl())
            ->assertSessionHas('status', 'Declined. The e-mail to the applicant could not be sent.');

        $this->assertSame(MembershipApplication::DECLINED, $this->application->fresh()->status);
        $this->assertNull(MembershipDecision::sole()->email_sent_at);
    }

    public function test_e_mail_errors_never_write_the_address_into_the_log(): void
    {
        Log::spy();
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('550 <tomas@example.test> rejected'));

        $this->actingAs($this->admin)->post($this->declineUrl());
        $other = MembershipApplication::factory()->create(['email' => 'other@example.test']);
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('550 <other@example.test> rejected'));
        $this->actingAs($this->admin)->post($this->approveUrl($other));

        Log::shouldHaveReceived('warning')->twice();
        Log::shouldHaveReceived('warning')->withArgs(fn ($message, $context) => ! preg_match('/tomas@|other@/', json_encode($context)));
    }

    public function test_the_decided_list_shows_only_the_last_30_days(): void
    {
        $this->actingAs($this->admin)->post($this->declineUrl());
        $this->actingAs($this->admin)->get('/admin/members')->assertSee('Tomás Herrera');

        $this->travel(31)->days();
        $this->actingAs($this->admin)->get('/admin/members')->assertDontSee('Tomás Herrera')->assertDontSee('Decided in the last');
    }

    public function test_declining_marks_the_application_logs_and_answers_the_applicant(): void
    {
        $this->actingAs($this->admin)->post($this->declineUrl())->assertSessionHas('status', 'Declined.');

        $application = $this->application->fresh();
        $this->assertSame(MembershipApplication::DECLINED, $application->status);
        $this->assertNotNull($application->decided_at);
        $this->assertSame(MembershipDecision::DECLINED, MembershipDecision::sole()->decision);
        $this->assertNull(MembershipDecision::sole()->member_id);
        $this->assertDatabaseCount('users', 1);
        Mail::assertSent(ApplicationDeclined::class, fn ($mail) => $mail->hasTo('tomas@example.test'));

        $this->actingAs($this->admin)->get('/admin/members')
            ->assertSee('Declined')
            ->assertSee('No applications wait for the board.');
    }

    public function test_the_moment_of_agreement_survives_the_decision(): void
    {
        // On MySQL with explicit_defaults_for_timestamp=OFF this column used to be overwritten
        // on every update. The GitHub run on MySQL repeats this test with that setting.
        $agreed = '2026-11-01 09:15:00';
        $this->application->forceFill(['agreed_at' => $agreed])->save();
        $other = MembershipApplication::factory()->create(['agreed_at' => $agreed]);

        $this->travel(1)->days();
        $this->actingAs($this->admin)->post($this->declineUrl());
        $this->assertSame($agreed, $this->application->fresh()->agreed_at->format('Y-m-d H:i:s'));

        $this->actingAs($this->admin)->post($this->approveUrl($other));
        $this->assertSame($agreed, MembershipDecision::where('decision', MembershipDecision::APPROVED)->sole()->agreed_at->format('Y-m-d H:i:s'));
    }

    public function test_a_declined_applicant_can_apply_again(): void
    {
        $this->actingAs($this->admin)->post($this->declineUrl());

        $this->assertFalse(MembershipApplication::addressIsTaken('tomas@example.test'));
    }

    public function test_approved_members_are_listed_as_decided(): void
    {
        $this->actingAs($this->admin)->post($this->approveUrl());

        $this->actingAs($this->admin)->get('/admin/members')
            ->assertSee('Approved, welcome e-mail sent')
            ->assertSee('Tomás Herrera');
    }

    public function test_the_log_survives_the_deletion_and_a_deleted_admin(): void
    {
        $this->actingAs($this->admin)->post($this->declineUrl());
        $this->travel(31)->days();
        $this->artisan('ern:delete-declined-applications')->assertSuccessful();
        $this->assertDatabaseCount('membership_applications', 0);

        $this->admin->delete();

        $decision = MembershipDecision::sole();
        $this->assertSame(MembershipDecision::DECLINED, $decision->decision);
        $this->assertNull($decision->decided_by);
    }

    public function test_an_unknown_application_answers_404(): void
    {
        $this->actingAs($this->admin)->post('/admin/members/applications/999999/approve')->assertNotFound();
    }
}
