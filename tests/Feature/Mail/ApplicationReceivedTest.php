<?php

namespace Tests\Feature\Mail;

use App\Mail\ApplicationReceived;
use App\Models\MembershipApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationReceivedTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_an_html_and_a_plain_text_version_with_a_copy_of_the_details(): void
    {
        $application = MembershipApplication::factory()->create([
            'first_name' => 'Ada', 'last_name' => 'Example', 'institution' => 'Example University (example)',
            'country' => 'Norway', 'city' => 'Kristiansand', 'career_stage' => 'PhD student', 'discord_name' => 'ada.example',
        ]);
        $mail = new ApplicationReceived($application);

        $mail->assertHasSubject('Your application to the Esports Research Network');
        $this->assertSame('mail.application-received', $mail->content()->view);
        $this->assertSame('mail.application-received-text', $mail->content()->text);

        foreach (['Hello Ada', 'Example University (example)', 'Kristiansand', 'PhD student', 'ada.example', 'The board confirms every application.', 'If you did not apply'] as $text) {
            $mail->assertSeeInHtml($text);
            $mail->assertSeeInText($text);
        }
    }

    public function test_html_in_the_details_is_escaped(): void
    {
        $mail = new ApplicationReceived(MembershipApplication::factory()->create(['first_name' => '<script>alert(1)</script>']));

        $mail->assertDontSeeInHtml('<script>alert(1)</script>', false);
        $mail->assertSeeInHtml('&lt;script&gt;', false);
    }

    public function test_it_holds_no_link_with_a_token_and_promises_no_approval_time(): void
    {
        $mail = new ApplicationReceived(MembershipApplication::factory()->create());
        $html = $mail->render();

        $this->assertStringNotContainsString('href=', $html);
        $this->assertStringNotContainsString('token', $html);
        $this->assertDoesNotMatchRegularExpression('/\b(days?|weeks?|within)\b/i', strip_tags($html));
    }
}
