<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ContentSeeder::class);
    }

    public function test_the_code_of_conduct_answers_with_its_sections(): void
    {
        $this->get('/code-of-conduct')->assertOk()
            ->assertSee('<h1>Code of Conduct</h1>', false)
            ->assertSee('Version 1.1, in force since 24 August 2026.')
            ->assertSee('harassment-free environment')
            ->assertSee('<h2 id="sec-expected-behavior">Expected Behavior</h2>', false)
            ->assertSee('href="#sec-expected-behavior"', false)
            ->assertSee('href="#sec-anti-retaliation-policy"', false)
            ->assertSee('Something happened?')
            ->assertSee('Report a concern');
    }

    public function test_the_imprint_answers(): void
    {
        $this->get('/imprint')->assertOk()
            ->assertSee('Esports Research Network e.V. (ERN e.V.)')
            ->assertSee('Registered Association (Vereinsnummer): VR 6678')
            ->assertSee('Legal Representative');
    }

    public function test_the_privacy_page_shows_the_placeholder_until_the_board_delivers(): void
    {
        $this->get('/privacy')->assertOk()
            ->assertSee('<h1>Privacy Policy</h1>', false)
            ->assertSee(Page::PRIVACY_PLACEHOLDER);
    }

    public function test_the_footer_links_imprint_and_privacy(): void
    {
        $this->get('/')->assertSee('href="'.url('/imprint').'"', false)->assertSee('href="'.url('/privacy').'"', false);
    }

    public function test_the_pages_follow_an_editors_change(): void
    {
        Page::where('slug', 'code-of-conduct')->update(['body' => "## New rules\n\nBe kind."]);

        $this->get('/code-of-conduct')->assertSee('<h2 id="sec-new-rules">New rules</h2>', false)
            ->assertDontSee('Expected Behavior')
            ->assertDontSee('On this page');
    }

    public function test_the_live_check_refuses_with_the_placeholder(): void
    {
        $this->artisan('ern:check-launch')
            ->expectsOutputToContain('the privacy policy is still the placeholder')
            ->assertFailed();
    }

    public function test_the_live_check_refuses_without_a_privacy_page(): void
    {
        Page::where('slug', 'privacy')->delete();

        $this->artisan('ern:check-launch')->assertFailed();
    }

    public function test_the_live_check_passes_once_the_real_text_is_in(): void
    {
        Page::where('slug', 'privacy')->update(['body' => "## Controller\n\nEsports Research Network e.V. …"]);

        $this->artisan('ern:check-launch')->expectsOutputToContain('Ready to go live')->assertSuccessful();
    }

    public function test_the_pages_answer_404_before_they_are_seeded(): void
    {
        Page::query()->delete();

        $this->get('/imprint')->assertNotFound();
        $this->get('/privacy')->assertNotFound();
        $this->get('/code-of-conduct')->assertNotFound();
    }

    public function test_headings_with_the_same_name_get_different_anchors(): void
    {
        Page::where('slug', 'code-of-conduct')->update(['body' => '## Rules

A

## Rules

B']);

        $this->get('/code-of-conduct')
            ->assertSee('id="sec-rules"', false)
            ->assertSee('id="sec-rules-2"', false)
            ->assertSee('href="#sec-rules-2"', false);
    }

    public function test_the_live_check_refuses_a_slightly_edited_placeholder(): void
    {
        Page::where('slug', 'privacy')->update(['body' => 'Our privacy policy is coming. Until then: [old policy](https://esportsresearch.net/imprint/).']);

        $this->artisan('ern:check-launch')->assertFailed();
    }
}
