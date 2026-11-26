<?php

namespace Tests\Feature;

use App\Models\News;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    private function seedConferences(): void
    {
        $this->seed(ContentSeeder::class);
        News::query()->delete();
    }

    private function makePost(string $title, string $status, ?string $date): News
    {
        $news = new News(['title' => $title, 'summary' => 'Summary of '.$title, 'status' => $status, 'published_at' => $date]);
        $news->slug = News::uniqueSlug($title);
        $news->save();

        return $news;
    }

    public function test_the_start_page_shows_every_block_of_the_mockup(): void
    {
        $this->seedConferences();
        $this->makePost('A post', News::PUBLISHED, '2026-10-01');

        $this->get('/')
            ->assertOk()
            ->assertSee('Research-focused.')
            ->assertSee('Globally connected.')
            ->assertSee('Join ERN')
            ->assertSee('ERNC27 Details')
            ->assertSee('Find research')
            ->assertSee('Teach with ERN')
            ->assertSee('Join and contribute')
            ->assertSee('Featured Event')
            ->assertSee('ERNC27: Think &amp; Do: Where Research, Competition &amp; Community Converge', false)
            ->assertSee('NC State University, Raleigh, USA')
            ->assertSee('News &amp; Updates', false)
            ->assertSee('What ERN Does')
            ->assertSee('Connect Researchers Worldwide')
            ->assertSee('Get Involved')
            ->assertSee('Become a Member')
            ->assertDontSee('For members')
            ->assertSee('href="'.url('/conferences/ernc27/call').'"', false)
            ->assertSee('href="'.url('/code-of-conduct').'"', false)
            ->assertSee('href="'.url('/join').'"', false);
    }

    public function test_it_shows_the_three_latest_published_news_and_no_drafts(): void
    {
        $this->makePost('Oldest', News::PUBLISHED, '2026-01-01');
        $this->makePost('Second', News::PUBLISHED, '2026-02-01');
        $this->makePost('Third', News::PUBLISHED, '2026-03-01');
        $this->makePost('Newest', News::PUBLISHED, '2026-04-01');
        $this->makePost('A draft', News::DRAFT, '2026-05-01');
        $this->makePost('Next year', News::PUBLISHED, now()->addYear()->toDateString());

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Newest', $html);
        $this->assertStringContainsString('Third', $html);
        $this->assertStringContainsString('Second', $html);
        $this->assertStringNotContainsString('Oldest', $html);
        $this->assertStringNotContainsString('A draft', $html);
        $this->assertStringNotContainsString('Next year', $html);
        $this->assertLessThan(strpos($html, 'Third'), strpos($html, 'Newest'), 'Newest first');
        $this->assertStringContainsString('href="'.url('/news/newest').'"', $html);
    }

    public function test_without_news_the_block_is_left_out(): void
    {
        $this->get('/')->assertOk()->assertDontSee('News &amp; Updates', false);
    }

    public function test_coming_up_lists_the_next_key_dates_only(): void
    {
        $this->seedConferences();
        $this->travelTo('2026-12-01 12:00');

        $this->get('/')
            ->assertSee('Coming up')
            ->assertSee('ERNC27: abstract deadline')
            ->assertSee('Monday 4 January 2027')
            ->assertSee('ERNC27: registration opens')
            ->assertDontSee('ERNC27: submissions open');

        // At most three.
        $this->travelTo('2026-10-01 12:00');
        $this->get('/')->assertSee('ERNC27: submissions open')->assertDontSee('ERNC27: registration opens');

        // After the key dates, the conference itself is still coming up.
        $this->travelTo('2027-05-01 12:00');
        $this->get('/')->assertSee('Coming up')->assertSee('9 to 11 November 2027');

        $this->travelTo('2027-11-12 12:00');
        $this->get('/')->assertDontSee('Coming up');
    }

    public function test_without_an_upcoming_conference_the_featured_block_is_left_out(): void
    {
        $this->get('/')->assertOk()->assertDontSee('Featured Event')->assertDontSee('Coming up');
    }

    public function test_the_start_page_has_one_h1_and_loads_nothing_from_other_sites(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertDoesNotMatchRegularExpression('/<(script|link|img|iframe)[^>]+(src|href)="(https?:)?\/\/(?!localhost)/i', $html);
    }
}
