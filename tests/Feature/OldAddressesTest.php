<?php

namespace Tests\Feature;

use App\Models\News;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OldAddressesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ContentSeeder::class);
    }

    public function test_each_listed_address_redirects_once_permanently_to_a_page_that_answers(): void
    {
        $this->assertNotEmpty(config('redirects'));

        foreach (config('redirects') as $old => $new) {
            foreach (['/'.$old, '/'.$old.'/'] as $address) {
                $response = $this->get($address);
                $response->assertStatus(301)->assertRedirect($new);

                // Once: the target answers directly, with no second redirect.
                $this->get($new)->assertOk();
            }
        }
    }

    public function test_the_known_addresses_of_the_task_work(): void
    {
        $this->get('/conference')->assertRedirect('/conferences')->assertStatus(301);
        $this->get('/getinvolved/')->assertRedirect('/get-involved');

        // Same address on the new site: no redirect needed.
        foreach (['/about', '/get-involved', '/news', '/imprint', '/about/', '/imprint/'] as $same) {
            $this->get($same)->assertOk();
        }
    }

    public function test_old_news_addresses_lead_to_the_post(): void
    {
        foreach (json_decode(file_get_contents(database_path('seeders/content/news.json')), true) as $post) {
            $path = parse_url($post['old_address'], PHP_URL_PATH);

            $response = $this->get($path);
            $response->assertStatus(301);
            $this->assertSame(route('news.show', $post['slug']), $response->headers->get('Location'));
            $this->get(route('news.show', $post['slug']))->assertOk();
        }
    }

    public function test_an_old_news_address_without_a_public_post_answers_404(): void
    {
        $this->get('/2026/01/01/a-post-that-never-existed/')->assertNotFound();

        News::where('slug', 'get-involved-with-the-esports-research-network-ern-board')->update(['status' => News::DRAFT]);
        $this->get('/2026/08/10/get-involved-with-the-esports-research-network-ern-board/')->assertNotFound();
    }

    public function test_redirects_go_only_to_addresses_on_this_site(): void
    {
        foreach (config('redirects') as $new) {
            $this->assertMatchesRegularExpression('#^/(?![/\\\\])#', $new);
        }
    }
}
