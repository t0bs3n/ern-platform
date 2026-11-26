<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MediaPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ContentSeeder::class);
    }

    public function test_the_podcast_page_shows_the_episodes_and_platforms(): void
    {
        $this->get('/podcast')->assertOk()
            ->assertSee('Podcast: The Esports Research Report')
            ->assertSee('The Esports Research Report is a podcast dedicated to showcasing')
            ->assertSee('Dr. Brian McCauley')
            ->assertSee('Episode 5')
            ->assertSee('Dr. Kurtis Homan')
            ->assertSee('href="https://www.youtube.com/watch?v=rZn2O_5lATI"', false)
            ->assertSee('All episodes on YouTube')
            ->assertSeeInOrder(['Episode 1', 'Raphael Priftis', 'Episode 5', 'Dr. Kurtis Homan']);
    }

    public function test_the_podcast_page_works_without_its_introduction(): void
    {
        Page::where('slug', 'podcast-intro')->delete();

        $this->get('/podcast')->assertOk()->assertSee('Episodes');
    }

    public function test_the_live_page_shows_the_stream_link_and_what_is_on(): void
    {
        config(['ern.live.next_stream' => 'ERN Lecture Series, Wednesday 8 September 2027, 16:00 (Oslo time)']);

        $this->get('/live')->assertOk()
            ->assertSee('<h1>ERN Live</h1>', false)
            ->assertSee('Next stream: ERN Lecture Series, Wednesday 8 September 2027, 16:00 (Oslo time).')
            ->assertSee('href="https://www.twitch.tv/esportsresearchnetwork"', false)
            ->assertSee('data-live-start hidden', false)
            ->assertSee('Recordings')
            ->assertSee('Good to know');
    }

    public function test_without_a_next_stream_the_line_is_left_out(): void
    {
        config(['ern.live.next_stream' => '']);

        $this->get('/live')->assertOk()->assertDontSee('Next stream:');
    }

    /** @return array<string, array{string}> */
    public static function pages(): array
    {
        return ['podcast' => ['/podcast'], 'live' => ['/live']];
    }

    #[DataProvider('pages')]
    public function test_no_external_script_or_frame_in_the_first_response(string $url): void
    {
        $html = $this->get($url)->assertOk()->getContent();

        $this->assertStringNotContainsStringIgnoringCase('<iframe', $html);
        $this->assertDoesNotMatchRegularExpression('/<script[^>]+src="(https?:)?\/\/(?!localhost)/i', $html);
        $this->assertDoesNotMatchRegularExpression('/<(img|link|video|audio|source)[^>]+(src|href)="(https?:)?\/\/(?!localhost)/i', $html);
        $this->assertStringNotContainsString('player.twitch.tv', $html);
        $this->assertStringNotContainsString('youtube.com/embed', $html);
    }
}
