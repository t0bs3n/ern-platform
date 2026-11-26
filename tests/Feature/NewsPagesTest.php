<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsPagesTest extends TestCase
{
    use RefreshDatabase;

    private function makePost(string $title, string $status = News::PUBLISHED, ?string $date = '2026-10-01', array $extra = []): News
    {
        $news = new News($extra + ['title' => $title, 'summary' => 'Summary of '.$title, 'status' => $status, 'published_at' => $date]);
        $news->slug = News::uniqueSlug($title);
        $news->save();

        return $news;
    }

    public function test_the_news_list_shows_published_posts_newest_first(): void
    {
        $this->makePost('Older post', date: '2026-01-01');
        $this->makePost('Newer post', date: '2026-06-01');
        $this->makePost('A draft', News::DRAFT, '2026-07-01');
        $this->makePost('A future post', date: now()->addMonth()->toDateString());

        $html = $this->get('/news')->assertOk()
            ->assertSee('News')
            ->assertSee('Stay up to date with the latest announcements and updates from the ERN community')
            ->assertSee('More for members')
            ->getContent();

        $this->assertStringContainsString('Newer post', $html);
        $this->assertStringNotContainsString('A draft', $html);
        $this->assertStringNotContainsString('A future post', $html);
        $this->assertLessThan(strpos($html, 'Older post'), strpos($html, 'Newer post'));
    }

    public function test_the_news_list_has_pages(): void
    {
        foreach (range(1, 14) as $n) {
            $this->makePost('Post number '.$n, date: '2026-01-'.str_pad((string) $n, 2, '0', STR_PAD_LEFT));
        }

        $this->get('/news')->assertOk()
            ->assertSee('Post number 14')
            ->assertDontSee('Post number 2<', false)
            ->assertSee('Older posts')
            ->assertSee('aria-current="page">1</span>', false);

        $this->get('/news?page=9')->assertNotFound();

        $this->get('/news?page=2')->assertOk()
            ->assertSee('Post number 1<', false)
            ->assertSee('Post number 2<', false)
            ->assertDontSee('Post number 14')
            ->assertSee('Newer posts');
    }

    public function test_a_single_post_shows_its_text(): void
    {
        $this->makePost('The call is open', extra: ['body' => "## Dates\n\nSubmit by **4 January**.", 'category' => 'ERNC27']);

        $this->get('/news/the-call-is-open')->assertOk()
            ->assertSee('<h1>The call is open</h1>', false)
            ->assertSee('<h2>Dates</h2>', false)
            ->assertSee('<strong>4 January</strong>', false)
            ->assertSee('1 October 2026')
            ->assertSee('ERNC27')
            ->assertSee('<title>The call is open | Esports Research Network</title>', false);
    }

    public function test_a_draft_and_a_future_post_answer_404_even_for_editors(): void
    {
        $this->makePost('Secret draft', News::DRAFT);
        $this->makePost('Not yet', date: now()->addDay()->toDateString());

        $this->get('/news/secret-draft')->assertNotFound();
        $this->get('/news/not-yet')->assertNotFound();
        $this->get('/news/no-such-post')->assertNotFound();
        $this->actingAs(User::factory()->withRoles(Role::Editor)->create())->get('/news/secret-draft')->assertNotFound();
    }

    public function test_the_feed_is_valid_rss_with_the_ten_latest_posts(): void
    {
        foreach (range(1, 12) as $n) {
            $this->makePost('Feed post '.$n, date: '2026-02-'.str_pad((string) $n, 2, '0', STR_PAD_LEFT));
        }
        $this->makePost('Draft & secret', News::DRAFT);

        $response = $this->get('/feed/news.xml')->assertOk();
        $this->assertStringStartsWith('application/rss+xml', $response->headers->get('Content-Type'));

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'The feed is well-formed XML.');
        $this->assertSame('2.0', (string) $xml['version']);
        $this->assertSame('Esports Research Network: News', (string) $xml->channel->title);
        $this->assertCount(10, $xml->channel->item);
        $this->assertSame('Feed post 12', (string) $xml->channel->item[0]->title);
        $this->assertSame(url('/news/feed-post-12'), (string) $xml->channel->item[0]->link);
        $this->assertNotEmpty((string) $xml->channel->item[0]->pubDate);
        $this->assertStringNotContainsString('Draft', $response->getContent());
    }

    public function test_the_feed_leaves_out_future_posts_and_carries_categories(): void
    {
        $this->makePost('Live post', extra: ['category' => 'ERNC27']);
        $this->makePost('Future post', date: now()->addWeek()->toDateString());

        $xml = simplexml_load_string($this->get('/feed/news.xml')->getContent());

        $this->assertCount(1, $xml->channel->item);
        $this->assertSame('ERNC27', (string) $xml->channel->item[0]->category);
    }

    public function test_an_empty_list_and_an_empty_feed_still_work(): void
    {
        $this->get('/news')->assertOk()->assertSee('No news yet.');

        $xml = simplexml_load_string($this->get('/feed/news.xml')->assertOk()->getContent());
        $this->assertNotFalse($xml);
        $this->assertCount(0, $xml->channel->item);
    }

    public function test_signed_in_members_see_their_own_box(): void
    {
        $this->actingAs(User::factory()->create())->get('/news')
            ->assertSee('More in your member area')
            ->assertDontSee('Become a member');
    }

    public function test_special_characters_are_escaped_in_the_feed(): void
    {
        $this->makePost('Research & <Practice>');

        $xml = simplexml_load_string($this->get('/feed/news.xml')->getContent());

        $this->assertSame('Research & <Practice>', (string) $xml->channel->item[0]->title);
    }

    public function test_the_news_list_points_feed_readers_to_the_feed(): void
    {
        $this->get('/news')->assertSee('<link rel="alternate" type="application/rss+xml" title="ERN News" href="'.url('/feed/news.xml').'">', false);
    }
}
