<?php

namespace Tests\Feature\Members;

use App\Mail\MemberMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DirectoryTest extends TestCase
{
    use RefreshDatabase;

    private User $me;

    private User $listed;

    private User $hidden;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        $this->me = User::factory()->create(['first_name' => 'Ada', 'last_name' => 'Viewer', 'slug' => 'ada-viewer', 'listed_in_directory' => true]);
        $this->listed = User::factory()->create([
            'first_name' => 'Rafael', 'last_name' => 'Monteiro', 'slug' => 'rafael-monteiro', 'email' => 'rafael@example.test',
            'country' => 'Brazil', 'career_stage' => 'Researcher with a PhD', 'institution' => 'Universidade do Sul (example)',
            'interests' => 'University esports, Survey research', 'orcid' => '0000-0002-1825-0097',
            'discord_name' => 'rafael.d', 'show_discord_in_directory' => false, 'open_to_collaboration' => true, 'listed_in_directory' => true,
        ]);
        $this->hidden = User::factory()->create([
            'first_name' => 'Hidden', 'last_name' => 'Person', 'slug' => 'hidden-person', 'email' => 'hidden@example.test',
            'country' => 'Iceland', 'interests' => 'Secret topic', 'listed_in_directory' => false,
        ]);
    }

    public function test_guests_are_sent_to_sign_in_alike_for_known_hidden_and_unknown_addresses(): void
    {
        foreach (['/members/directory', '/members/rafael-monteiro', '/members/hidden-person', '/members/nobody-here'] as $url) {
            $this->get($url)->assertRedirect('/members/sign-in');
        }
        $this->post('/members/rafael-monteiro/contact', ['contact_message' => 'Hi'])->assertRedirect('/members/sign-in');
        Mail::assertNothingSent();
    }

    public function test_unapproved_users_are_refused(): void
    {
        $this->me->forceFill(['approved_at' => null])->save();

        $this->actingAs($this->me)->get('/members/directory')->assertForbidden();
        $this->actingAs($this->me)->get('/members/rafael-monteiro')->assertForbidden();
        $this->actingAs($this->me)->post('/members/rafael-monteiro/contact', ['contact_message' => 'Hi'])->assertForbidden();
        Mail::assertNothingSent();
    }

    public function test_the_directory_shows_the_mockup_wording_and_listed_members_only(): void
    {
        $this->actingAs($this->me)->get('/members/directory')->assertOk()
            ->assertSee('Member directory')
            ->assertSee('Search the member directory')
            ->assertSee('Search by name, country or research topic')
            ->assertSee('Career stage')
            ->assertSee('Open a member to write through ERN by e-mail. Discord is optional.')
            ->assertSee('2 members')
            ->assertSee('Rafael Monteiro')
            ->assertSee('Open to: Collaboration')
            ->assertSee('University esports')
            ->assertDontSee('Hidden Person')
            ->assertDontSee('Iceland')
            ->assertDontSee('Secret topic')
            ->assertDontSee('rafael@example.test')
            ->assertDontSee('rafael.d');
    }

    public function test_an_unapproved_listed_user_appears_nowhere(): void
    {
        $this->listed->forceFill(['approved_at' => null])->save();

        $this->actingAs($this->me)->get('/members/directory')->assertDontSee('Rafael Monteiro')->assertDontSee('Brazil');
        $this->actingAs($this->me)->get('/members/rafael-monteiro')->assertNotFound();
    }

    public function test_search_and_filters(): void
    {
        $see = fn (string $query) => $this->actingAs($this->me)->get('/members/directory?'.$query);

        $see('q=monteiro')->assertSee('Rafael Monteiro')->assertSee('1 of 2 members')->assertSee('Clear search and filters');
        $see('q=brazil+survey')->assertSee('Rafael Monteiro');
        $see('q=brazil+nothing')->assertDontSee('Rafael Monteiro')->assertSee('No member found. Try a country or a topic, for example Japan or governance.');
        $see('q=hidden')->assertDontSee('Hidden Person');
        $see('q=rafael%40example.test')->assertSee('No member found.');
        $see('q=rafael.d')->assertSee('No member found.');
        $see('topic=Survey+research')->assertSee('Rafael Monteiro');
        $see('country=Brazil')->assertSee('Rafael Monteiro')->assertDontSee('Ada Viewer');
        $see('stage=Researcher+with+a+PhD')->assertSee('Rafael Monteiro');
        $see('open=collaboration')->assertSee('Rafael Monteiro')->assertDontSee('Ada Viewer');
        $see('country=Iceland')->assertDontSee('Hidden Person');
        // Unknown values are ignored rather than breaking the page.
        $see('stage=Wizard&open=everything')->assertOk()->assertSee('2 members');
    }

    public function test_like_wildcards_find_nothing_extra(): void
    {
        foreach (['%', '_', '!', '%25'] as $wildcard) {
            $this->actingAs($this->me)->get('/members/directory?q='.urlencode($wildcard))->assertSee('No member found.');
        }
    }

    public function test_the_filter_choices_never_show_a_hidden_member(): void
    {
        $this->actingAs($this->me)->get('/members/directory')
            ->assertSee('<option value="Brazil"', false)
            ->assertDontSee('<option value="Iceland"', false)
            ->assertDontSee('Secret topic');
    }

    public function test_pages_keep_the_filters(): void
    {
        User::factory()->count(25)->create(['country' => 'Brazil', 'listed_in_directory' => true]);

        $response = $this->actingAs($this->me)->get('/members/directory?country=Brazil')->assertSee('Next');
        $this->assertStringContainsString('country=Brazil', $response->getContent());
        $this->actingAs($this->me)->get('/members/directory?country=Brazil&page=2')->assertOk()->assertSee('Previous');
    }

    public function test_the_researcher_page_shows_released_fields_and_never_the_e_mail(): void
    {
        $this->actingAs($this->me)->get('/members/rafael-monteiro')->assertOk()
            ->assertSee('<h1 style="margin:0">Rafael Monteiro</h1>', false)
            ->assertSee('Researcher with a PhD · Brazil')
            ->assertSee('Universidade do Sul (example)')
            ->assertSee('Research interests')
            ->assertSee('Survey research')
            ->assertSee('Open to')
            ->assertSee('https://orcid.org/0000-0002-1825-0097')
            ->assertSee('Write to Rafael')
            ->assertSee('Send through ERN')
            ->assertSee('ERN forwards your message by e-mail. Your address is shown, theirs stays private until they answer.')
            ->assertDontSee('rafael@example.test')
            ->assertDontSee('On Discord');
    }

    public function test_discord_shows_only_when_released(): void
    {
        $this->listed->update(['show_discord_in_directory' => true]);

        $this->actingAs($this->me)->get('/members/rafael-monteiro')->assertSee('On Discord')->assertSee('rafael.d');
        $this->actingAs($this->me)->get('/members/directory')->assertSee('rafael.d');
    }

    public function test_a_hidden_member_answers_exactly_like_an_unknown_address(): void
    {
        $hidden = $this->actingAs($this->me)->get('/members/hidden-person')->assertNotFound()->getContent();
        $unknown = $this->actingAs($this->me)->get('/members/nobody-here')->assertNotFound()->getContent();

        $this->assertSame($unknown, $hidden);
        $this->actingAs($this->hidden)->get('/members/hidden-person')->assertNotFound();
    }

    public function test_fixed_member_addresses_still_work(): void
    {
        $this->actingAs($this->me)->get('/members/profile')->assertOk()->assertSee('My details');
        $this->actingAs($this->me)->get('/members/sign-out')->assertMethodNotAllowed();
        $this->actingAs($this->me)->get('/members/directory')->assertOk();
        $this->actingAs($this->me)->post('/members/directory/contact', ['contact_message' => 'Hi'])->assertNotFound();
        Mail::assertNothingSent();
    }

    public function test_the_own_page_has_no_contact_form(): void
    {
        $this->actingAs($this->me)->get('/members/ada-viewer')->assertOk()
            ->assertSee('This is your page as other members see it.')
            ->assertDontSee('Send through ERN');
        $this->actingAs($this->me)->post('/members/ada-viewer/contact', ['contact_message' => 'Hello me'])->assertForbidden();
        Mail::assertNothingSent();
    }

    public function test_a_message_goes_to_the_member_only_with_replies_to_the_sender(): void
    {
        $this->actingAs($this->me)->post('/members/rafael-monteiro/contact', ['contact_message' => 'Hello Rafael, shall we work together?'])
            ->assertRedirect('/members/rafael-monteiro')
            ->assertSessionHas('status', 'Your message is sent. Rafael gets it by e-mail and can answer you directly.');

        Mail::assertSent(MemberMessage::class, function (MemberMessage $mail) {
            return $mail->hasTo('rafael@example.test') && count($mail->to) === 1
                && $mail->cc === [] && $mail->bcc === []
                && $mail->hasReplyTo($this->me->email)
                && $mail->body === 'Hello Rafael, shall we work together?';
        });
        Mail::assertNotQueued(MemberMessage::class);
    }

    public function test_the_message_is_stored_nowhere(): void
    {
        // The real stores, so that these checks can fail.
        config(['session.driver' => 'database', 'queue.default' => 'database']);

        $this->actingAs($this->me)->post('/members/rafael-monteiro/contact', ['contact_message' => 'A very private note']);

        $this->assertDatabaseCount('jobs', 0);
        $this->assertGreaterThan(0, DB::table('sessions')->count());
        foreach (DB::table('sessions')->pluck('payload') as $payload) {
            $this->assertStringNotContainsString('A very private note', base64_decode($payload));
        }

        $this->actingAs($this->me)->from('/members/rafael-monteiro')->post('/members/rafael-monteiro/contact', ['contact_message' => str_repeat('x', 5001)])
            ->assertSessionHasErrors(['contact_message' => 'This is too long.']);
        $this->assertArrayNotHasKey('contact_message', session()->getOldInput());
    }

    public function test_hidden_unapproved_and_unknown_members_cannot_be_written_to(): void
    {
        $unapproved = User::factory()->create(['slug' => 'not-approved', 'listed_in_directory' => true]);
        $unapproved->forceFill(['approved_at' => null])->save();

        foreach (['hidden-person', 'not-approved', 'nobody-here'] as $slug) {
            $this->actingAs($this->me)->post('/members/'.$slug.'/contact', ['contact_message' => 'Hi'])->assertNotFound();
        }
        Mail::assertNothingSent();
    }

    public function test_an_empty_message_is_refused(): void
    {
        $this->actingAs($this->me)->post('/members/rafael-monteiro/contact', ['contact_message' => ''])
            ->assertSessionHasErrors(['contact_message' => 'Write a few words first.']);
        Mail::assertNothingSent();
    }

    public function test_messages_are_limited_per_pair_and_per_sender(): void
    {
        foreach (range(1, 3) as $n) {
            $this->actingAs($this->me)->post('/members/rafael-monteiro/contact', ['contact_message' => "Note {$n}"])->assertRedirect();
        }
        $this->actingAs($this->me)->post('/members/rafael-monteiro/contact', ['contact_message' => 'Note 4'])->assertStatus(429);

        $others = User::factory()->count(3)->create(['listed_in_directory' => true]);
        foreach ($others as $n => $other) {
            $response = $this->actingAs($this->me)->post('/members/'.$other->slug.'/contact', ['contact_message' => 'Hi']);
            $n < 2 ? $response->assertRedirect() : $response->assertStatus(429);
        }
        Mail::assertSentCount(5);
    }

    public function test_a_sender_writes_at_most_twenty_messages_a_day(): void
    {
        $others = User::factory()->count(21)->create(['listed_in_directory' => true]);

        foreach ($others as $n => $other) {
            if ($n > 0 && $n % 5 === 0) {
                $this->travel(61)->minutes(); // stay under the hourly limit
            }
            $response = $this->actingAs($this->me)->post('/members/'.$other->slug.'/contact', ['contact_message' => 'Hi']);
            $n < 20 ? $response->assertRedirect() : $response->assertStatus(429);
        }

        Mail::assertSentCount(20);
    }

    public function test_a_failed_send_keeps_the_text_says_so_and_logs_only_the_kind_of_error(): void
    {
        Log::spy();
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('550 <rafael@example.test> rejected'));

        $this->actingAs($this->me)->post('/members/rafael-monteiro/contact', ['contact_message' => 'Please keep this text'])
            ->assertStatus(503)
            ->assertSee('Your message could not be sent. Please try again later.')
            ->assertSee('Please keep this text');

        Log::shouldHaveReceived('warning')->withArgs(fn ($message, $context) => ! str_contains(json_encode($context), 'example.test') && ! str_contains(json_encode($context), 'Please keep'));
    }

    public function test_the_e_mail_escapes_html_and_links_the_sender_page_only_when_listed(): void
    {
        $mail = new MemberMessage($this->me, $this->listed, '<script>alert(1)</script>');
        $mail->assertHasSubject('Ada Viewer wrote to you through ERN');
        $mail->assertSeeInHtml('&lt;script&gt;', false);
        $mail->assertDontSeeInHtml('<script>alert(1)</script>', false);
        $mail->assertSeeInHtml('/members/ada-viewer');
        $mail->assertSeeInText('Answer this e-mail to reply to Ada.');
        $mail->assertDontSeeInHtml('rafael@example.test');

        $this->me->update(['listed_in_directory' => false]);
        (new MemberMessage($this->me->fresh(), $this->listed, 'Hi'))->assertDontSeeInHtml('/members/ada-viewer');
    }
}
