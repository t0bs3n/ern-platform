<?php

namespace Tests\Feature;

use App\Mail\ApplicationReceived;
use App\Models\MembershipApplication;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class JoinTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutDefer();
        Mail::fake();
    }

    /** @return array<string, string> */
    private function valid(array $override = []): array
    {
        return $override + [
            'first_name' => 'Ada',
            'last_name' => 'Example',
            'email' => 'ada@example.test',
            'institution' => 'Example University (example)',
            'country' => 'Norway',
            'city' => 'Kristiansand',
            'career_stage' => 'PhD student',
            'interests' => 'Esports psychology',
            'discord_name' => 'ada.example',
            'agree' => '1',
        ];
    }

    private function normalise(string $html): string
    {
        return preg_replace('/name="_token" value="[^"]*"/', '', $html);
    }

    public function test_join_page_shows_the_form_and_the_facts(): void
    {
        $this->get('/join')->assertOk()
            ->assertSee('<h1>Become a member</h1>', false)
            ->assertSee('Apply online. The board confirms every application.')
            ->assertSee('Fields marked with')
            ->assertSee('University or organization')
            ->assertSee('Please choose')
            ->assertSee('A few keywords are enough. Other members find you by them.')
            ->assertSee('Optional. With it you get the member role on the ERN Discord.')
            ->assertSee('href="'.url('/code-of-conduct').'"', false)
            ->assertSee('href="'.url('/privacy').'"', false)
            ->assertSee('Send application')
            ->assertSee('Membership is free.')
            ->assertSee('What happens next')
            ->assertSee('name="_token"', false);
    }

    public function test_the_page_works_for_a_guest_and_a_member(): void
    {
        $this->get('/join')->assertOk();
        $this->actingAs(User::factory()->create())->get('/join')->assertOk();
    }

    public function test_a_valid_application_is_stored_with_its_agreement_time_and_confirmed(): void
    {
        $this->travelTo('2026-11-26 10:00:00');

        $this->post('/join', $this->valid(['email' => 'Ada@Example.TEST']))
            ->assertRedirect('/join')
            ->assertSessionHas('joined', 'Ada');

        $application = MembershipApplication::sole();
        $this->assertSame('ada@example.test', $application->email);
        $this->assertSame('2026-11-26 10:00:00', $application->agreed_at->format('Y-m-d H:i:s'));
        $this->assertSame(MembershipApplication::OPEN, $application->status);
        $this->assertSame('Kristiansand', $application->city);

        Mail::assertSent(ApplicationReceived::class, fn ($mail) => $mail->hasTo('ada@example.test') && $mail->application->is($application));
        $this->assertDatabaseCount('users', 0);
    }

    public function test_the_confirmation_shows_the_name_and_the_timeline_and_no_approval_time(): void
    {
        $this->post('/join', $this->valid());

        $html = $this->get('/join')->assertOk()
            ->assertSee('Thank you, Ada. Your application is received')
            ->assertSee('We sent a copy to your e-mail address.')
            ->assertSee('Waiting for the board')
            ->assertSee('No e-mail from us?')
            ->assertSee('Other ways to get involved')
            ->assertDontSee('Send application')
            ->getContent();

        $this->assertDoesNotMatchRegularExpression('/\b(days?|weeks?|within)\b/i', strip_tags($html));
        $this->get('/join')->assertSee('Send application');
    }

    /** @return array<string, array{string, string}> */
    public static function requiredFields(): array
    {
        return [
            'first name' => ['first_name', 'This field is needed.'],
            'last name' => ['last_name', 'This field is needed.'],
            'e-mail' => ['email', 'This field is needed.'],
            'institution' => ['institution', 'This field is needed.'],
            'country' => ['country', 'This field is needed.'],
            'career stage' => ['career_stage', 'Please choose one.'],
            'agreement' => ['agree', 'Please confirm that you have read both.'],
        ];
    }

    #[DataProvider('requiredFields')]
    public function test_each_required_field_is_enforced(string $field, string $message): void
    {
        $data = $this->valid();
        unset($data[$field]);

        $this->from('/join')->post('/join', $data)->assertRedirect('/join')->assertSessionHasErrors([$field => $message]);

        $this->assertDatabaseCount('membership_applications', 0);
        Mail::assertNothingSent();
    }

    public function test_wrong_values_are_refused_with_the_mockup_messages(): void
    {
        $this->post('/join', $this->valid(['email' => 'not an address', 'career_stage' => 'Wizard', 'agree' => '0', 'first_name' => str_repeat('a', 101)]))
            ->assertSessionHasErrors([
                'email' => 'Enter an e-mail address, for example you@university.edu.',
                'career_stage' => 'Please choose one.',
                'agree' => 'Please confirm that you have read both.',
                'first_name' => 'This is too long.',
            ]);

        $this->assertDatabaseCount('membership_applications', 0);
    }

    public function test_optional_fields_may_be_empty(): void
    {
        $this->post('/join', $this->valid(['city' => '', 'interests' => '', 'discord_name' => '']))->assertSessionHasNoErrors();

        $this->assertNull(MembershipApplication::sole()->city);
    }

    public function test_status_and_agreement_time_cannot_be_set_from_outside(): void
    {
        $this->travelTo('2026-11-26 10:00:00');

        $this->post('/join', $this->valid(['status' => 'approved', 'agreed_at' => '2020-01-01 00:00:00']));

        $application = MembershipApplication::sole();
        $this->assertSame(MembershipApplication::OPEN, $application->status);
        $this->assertSame('2026-11-26', $application->agreed_at->toDateString());
    }

    public function test_errors_keep_the_input_and_mark_the_fields(): void
    {
        $this->from('/join')->post('/join', $this->valid(['country' => '']));

        $this->get('/join')
            ->assertSee('Please check the marked fields.')
            ->assertSee('value="Ada"', false)
            ->assertSee('aria-invalid="true"', false)
            ->assertSee('aria-describedby="f-country-err"', false)
            ->assertSee('<span class="x-err" id="f-country-err">This field is needed.</span>', false);
    }

    /** @return array<string, array{\Closure}> */
    public static function takenAddresses(): array
    {
        return [
            'an approved member' => [fn () => User::factory()->create(['email' => 'ada@example.test'])],
            'an unapproved user' => [fn () => User::factory()->unapproved()->create(['email' => 'ada@example.test'])],
            'an open application' => [fn () => MembershipApplication::factory()->create(['email' => 'ada@example.test'])],
            'an open application, other capitals' => [fn () => MembershipApplication::factory()->create(['email' => 'ADA@example.test'])],
        ];
    }

    #[DataProvider('takenAddresses')]
    public function test_a_taken_address_creates_nothing_and_answers_the_same(\Closure $setUp): void
    {
        $this->post('/join', $this->valid(['email' => 'new@example.test']));
        $newAnswer = $this->normalise($this->get('/join')->getContent());
        MembershipApplication::query()->delete();
        Mail::fake();

        $setUp();
        $before = MembershipApplication::count();

        $response = $this->post('/join', $this->valid(['email' => 'Ada@example.test']));

        $response->assertRedirect('/join')->assertSessionHas('joined', 'Ada');
        $this->assertSame($newAnswer, $this->normalise($this->get('/join')->getContent()));
        $this->assertSame($before, MembershipApplication::count());
        Mail::assertNothingSent();
    }

    public function test_a_filled_honeypot_stores_nothing_and_answers_the_same(): void
    {
        $this->post('/join', $this->valid(['email' => 'new@example.test']));
        $newAnswer = $this->normalise($this->get('/join')->getContent());
        MembershipApplication::query()->delete();
        Mail::fake();

        $this->post('/join', $this->valid(['website' => 'https://spam.example']))
            ->assertRedirect('/join')
            ->assertSessionHas('joined', 'Ada');

        $this->assertDatabaseCount('membership_applications', 0);
        Mail::assertNothingSent();
        $this->assertSame($newAnswer, $this->normalise($this->get('/join')->getContent()));
    }

    public function test_a_failing_e_mail_still_gives_the_confirmation(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP is down'));

        $this->post('/join', $this->valid())->assertRedirect('/join')->assertSessionHas('joined', 'Ada');

        $this->assertDatabaseCount('membership_applications', 1);
    }

    public function test_the_honeypot_is_hidden_from_people(): void
    {
        $this->get('/join')
            ->assertSee('<div class="x-hp" aria-hidden="true">', false)
            ->assertSee('name="website" type="text" value="" tabindex="-1" autocomplete="off"', false);
    }

    public function test_the_rate_limit_per_internet_address(): void
    {
        foreach (range(1, 5) as $n) {
            $this->post('/join', $this->valid(['email' => "person{$n}@example.test"]))->assertRedirect('/join');
        }

        $this->post('/join', $this->valid(['email' => 'person6@example.test']))
            ->assertStatus(429)
            ->assertSee('Too many tries');
        $this->assertDatabaseCount('membership_applications', 5);
    }

    public function test_the_rate_limit_per_e_mail_address_counts_duplicates_too(): void
    {
        foreach (range(1, 3) as $n) {
            $this->withServerVariables(['REMOTE_ADDR' => "10.0.0.{$n}"])
                ->post('/join', $this->valid())->assertRedirect('/join');
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.9'])
            ->post('/join', $this->valid(['email' => 'ADA@example.test']))
            ->assertStatus(429);
        $this->assertDatabaseCount('membership_applications', 1);
    }

    public function test_invalid_sends_count_against_the_limit(): void
    {
        foreach (range(1, 5) as $n) {
            $this->post('/join', ['email' => "invalid{$n}@example.test"])->assertSessionHasErrors('first_name');
        }

        $this->post('/join', $this->valid())->assertStatus(429);
    }

    public function test_the_form_carries_the_csrf_token(): void
    {
        $this->get('/join')->assertSee('name="_token"', false);
    }

    public function test_the_agreement_links_lead_to_existing_pages(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/code-of-conduct')->assertOk();
        $this->get('/privacy')->assertOk();
    }
}
