<?php

namespace Tests\Feature\Mail;

use App\Mail\PasswordResetLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_an_html_and_a_plain_text_version(): void
    {
        $mail = new PasswordResetLink(User::factory()->create(), 'token-123');

        $mail->assertHasSubject('Set a new password for the ERN member area');
        $this->assertSame('mail.password-reset', $mail->content()->view);
        $this->assertSame('mail.password-reset-text', $mail->content()->text);

        $text = view('mail.password-reset-text', ['user' => $mail->user, 'url' => $mail->url, 'minutes' => $mail->minutes])->render();
        $this->assertStringContainsString($mail->url, $text);
        $this->assertStringNotContainsString('&amp;', $text);
    }

    public function test_it_contains_the_link_and_no_password_or_profile_data(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Ada', 'email' => 'ada@example.test',
            'institution' => 'Somewhere University (example)', 'country' => 'Iceland',
        ]);
        $mail = new PasswordResetLink($user, 'token-123');

        $mail->assertSeeInHtml('Hello Ada');
        $mail->assertSeeInHtml('/members/reset-password/token-123');
        $mail->assertSeeInHtml('60 minutes');
        $mail->assertSeeInText('/members/reset-password/token-123');
        $mail->assertDontSeeInHtml('Somewhere University');
        $mail->assertDontSeeInHtml('Iceland');
        $mail->assertDontSeeInHtml('password:');
        $mail->assertDontSeeInText('Somewhere University');
    }
}
