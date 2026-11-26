<?php

namespace Tests\Feature\Members;

use App\Mail\ApplicationDeclined;
use App\Mail\WelcomeMember;
use App\Members\ActivationLink;
use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalHelpersTest extends TestCase
{
    use RefreshDatabase;

    public function test_slugs_come_from_the_name_and_avoid_reserved_and_taken_ones(): void
    {
        $this->assertSame('tomas-herrera', User::uniqueSlug('Tomás', 'Herrera'));
        $this->assertSame('sign-in-2', User::uniqueSlug('Sign', 'In'));
        $this->assertSame('member', User::uniqueSlug('李', '华'));

        User::factory()->create(['slug' => 'ada-example']);
        User::factory()->create(['slug' => 'ada-example-2']);
        $this->assertSame('ada-example-3', User::uniqueSlug('Ada', 'Example'));
    }

    public function test_the_activation_link_works_expires_and_cannot_be_changed(): void
    {
        $member = User::factory()->withoutPassword()->create();
        $other = User::factory()->withoutPassword()->create();
        $link = ActivationLink::for($member);

        $this->get($link)->assertOk()->assertSee('Welcome to ERN');
        $this->get(str_replace('/activate/'.$member->id, '/activate/'.$other->id, $link))->assertForbidden();
        $this->get($link.'x')->assertForbidden();

        $this->travel(7)->days();
        $this->travel(1)->minutes();
        $this->get($link)->assertForbidden();
    }

    public function test_the_deletion_job_deletes_declined_applications_after_30_days_only(): void
    {
        $old = MembershipApplication::factory()->create(['status' => MembershipApplication::DECLINED, 'decided_at' => now()->subDays(30)->subMinute()]);
        $recent = MembershipApplication::factory()->create(['status' => MembershipApplication::DECLINED, 'decided_at' => now()->subDays(29)]);
        $open = MembershipApplication::factory()->create(['created_at' => now()->subYear()]);

        $this->artisan('ern:delete-declined-applications')->expectsOutput('Deleted 1 declined application(s).')->assertSuccessful();

        $this->assertModelMissing($old);
        $this->assertModelExists($recent);
        $this->assertModelExists($open);
    }

    public function test_the_deletion_job_is_scheduled_daily(): void
    {
        $event = collect(app(Schedule::class)->events())->first(fn ($e) => str_contains($e->command ?? '', 'ern:delete-declined-applications'));

        $this->assertNotNull($event);
        $this->assertSame('0 0 * * *', $event->expression);
    }

    public function test_the_welcome_e_mail_has_both_versions_and_no_details(): void
    {
        $member = User::factory()->withoutPassword()->create(['first_name' => 'Noor', 'institution' => 'Secret University (example)']);
        $mail = new WelcomeMember($member);

        $mail->assertHasSubject('Welcome to the Esports Research Network');
        $this->assertSame('mail.welcome-member-text', $mail->content()->text);
        $mail->assertSeeInHtml('Welcome to ERN');
        $mail->assertSeeInHtml('The board has confirmed your membership. Choose a password to open the member area.');
        $mail->assertSeeInHtml('Set your password');
        $mail->assertSeeInText('/members/activate/'.$member->id);
        $mail->assertSeeInText('7 days');
        $mail->assertDontSeeInHtml('Secret University');
        $mail->assertDontSeeInText('Secret University');
    }

    public function test_the_declined_e_mail_names_no_decider_and_no_reason(): void
    {
        $mail = new ApplicationDeclined(MembershipApplication::factory()->create(['first_name' => '<b>Linh</b>']));

        $mail->assertHasSubject('Your application to the Esports Research Network');
        $mail->assertSeeInHtml('&lt;b&gt;Linh&lt;/b&gt;', false);
        $mail->assertSeeInHtml('The board has decided not to confirm your application.');
        $mail->assertSeeInText('30 days from now');
        $mail->assertDontSeeInHtml('reason');
        $mail->assertDontSeeInHtml('Admin');
    }
}
