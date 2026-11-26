<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\News;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContentEditingTest extends TestCase
{
    use RefreshDatabase;

    private function editor(): User
    {
        return User::factory()->withRoles(Role::Editor)->create();
    }

    /** @return array<string, array{string, string}> */
    public static function contentRoutes(): array
    {
        return [
            'content tab' => ['get', '/admin/content'],
            'new post' => ['get', '/admin/content/news/new'],
            'store post' => ['post', '/admin/content/news'],
            'edit post' => ['get', '/admin/content/news/{news}'],
            'update post' => ['put', '/admin/content/news/{news}'],
            'new page' => ['get', '/admin/content/pages/new'],
            'store page' => ['post', '/admin/content/pages'],
            'edit page' => ['get', '/admin/content/pages/{page}'],
            'update page' => ['put', '/admin/content/pages/{page}'],
        ];
    }

    private function address(string $url): string
    {
        $news = News::first() ?? $this->makeNews();
        $page = Page::first() ?? $this->makePage();

        return str_replace(['{news}', '{page}'], [$news->id, $page->id], $url);
    }

    private function makeNews(array $attributes = []): News
    {
        $news = new News($attributes + ['title' => 'A post', 'summary' => 'Short.', 'body' => 'Text.', 'status' => News::DRAFT]);
        $news->slug = $attributes['slug'] ?? News::uniqueSlug($news->title);
        $news->save();

        return $news;
    }

    private function makePage(): Page
    {
        $page = new Page(['title' => 'Imprint', 'body' => 'Who we are.']);
        $page->slug = 'imprint';
        $page->save();

        return $page;
    }

    #[DataProvider('contentRoutes')]
    public function test_a_member_and_other_admin_roles_are_refused(string $method, string $url): void
    {
        $url = $this->address($url);
        $payload = ['title' => 'Hacked', 'summary' => 'x', 'status' => 'published', 'body' => 'x', 'slug' => 'hacked'];

        $this->actingAs(User::factory()->create())->{$method}($url, $payload)->assertForbidden();
        $this->actingAs(User::factory()->withRoles(Role::DatabaseLead)->create())->{$method}($url, $payload)->assertForbidden();

        $this->assertDatabaseMissing('news', ['title' => 'Hacked']);
        $this->assertDatabaseMissing('pages', ['title' => 'Hacked']);
    }

    #[DataProvider('contentRoutes')]
    public function test_a_guest_is_sent_to_sign_in(string $method, string $url): void
    {
        $this->{$method}($this->address($url))->assertRedirect('/members/sign-in');
    }

    public function test_an_admin_may_edit_content_too(): void
    {
        $this->actingAs(User::factory()->withRoles(Role::Admin)->create())->get('/admin/content')->assertOk();
    }

    public function test_an_editor_creates_changes_and_publishes_a_post(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)->get('/admin/content')->assertOk()->assertSee('New post')->assertSee('Pages');

        $this->actingAs($editor)->post('/admin/content/news', [
            'title' => 'ERNC27 call is open',
            'summary' => 'Submit your abstract.',
            'body' => "## Dates\n\nThe deadline is **4 January 2027**.",
            'status' => News::DRAFT,
        ])->assertRedirect('/admin/content');

        $news = News::firstOrFail();
        $this->assertSame('ernc27-call-is-open', $news->slug);
        $this->assertSame(News::DRAFT, $news->status);
        $this->assertNull($news->published_at);
        $this->assertFalse(News::published()->exists());

        $this->actingAs($editor)->get('/admin/content/news/'.$news->id)->assertOk()->assertSee('ERNC27 call is open');

        $this->actingAs($editor)->put('/admin/content/news/'.$news->id, [
            'title' => 'ERNC27: the call is open',
            'summary' => 'Submit your abstract now.',
            'body' => $news->body,
            'status' => News::PUBLISHED,
        ])->assertRedirect('/admin/content');

        $news->refresh();
        $this->assertSame('ERNC27: the call is open', $news->title);
        $this->assertSame('ernc27-call-is-open', $news->slug, 'The address stays when the title changes.');
        $this->assertSame(News::PUBLISHED, $news->status);
        $this->assertNotNull($news->published_at);
        $this->assertTrue(News::published()->whereKey($news->id)->exists());
    }

    public function test_slugs_stay_unique(): void
    {
        $this->makeNews(['title' => 'Same title']);

        $this->actingAs($this->editor())->post('/admin/content/news', [
            'title' => 'Same title', 'summary' => 'x', 'status' => News::DRAFT,
        ]);

        $this->assertSame(['same-title', 'same-title-2'], News::orderBy('id')->pluck('slug')->all());
    }

    public function test_drafts_and_future_posts_are_invisible_to_the_public(): void
    {
        $this->makeNews(['title' => 'Draft', 'status' => News::DRAFT, 'published_at' => now()->subDay()]);
        $this->makeNews(['title' => 'Tomorrow', 'status' => News::PUBLISHED, 'published_at' => now()->addDay()]);
        $this->makeNews(['title' => 'Live', 'status' => News::PUBLISHED, 'published_at' => now()->subHour()]);

        $this->assertSame(['Live'], News::published()->pluck('title')->all());
    }

    public function test_the_preview_shows_the_text_and_saves_nothing(): void
    {
        $this->actingAs($this->editor())->post('/admin/content/news', [
            'title' => 'Preview me',
            'summary' => 'A summary.',
            'body' => "## A heading\n\nSome **bold** text.",
            'status' => News::PUBLISHED,
            'preview' => '1',
        ])->assertOk()
            ->assertSee('Preview')
            ->assertSee('not saved')
            ->assertSee('<h2>A heading</h2>', false)
            ->assertSee('<strong>bold</strong>', false)
            ->assertSee('value="Preview me"', false);

        $this->assertDatabaseCount('news', 0);
    }

    public function test_raw_html_and_unsafe_links_are_not_rendered(): void
    {
        $news = $this->makeNews(['body' => "<script>alert('x')</script>\n\n<b>bold</b>\n\n[click](javascript:alert(1))"]);

        $html = (string) $news->bodyHtml();

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<b>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('javascript:', $html);

        $this->actingAs($this->editor())->post('/admin/content/pages', [
            'title' => 'Test', 'slug' => 'test', 'body' => '<iframe src="https://example.com"></iframe>', 'preview' => '1',
        ])->assertOk()->assertDontSee('<iframe', false)->assertSee('&lt;iframe', false);
    }

    public function test_a_preview_while_editing_saves_nothing(): void
    {
        $editor = $this->editor();
        $news = $this->makeNews(['title' => 'Saved title']);
        $page = $this->makePage();

        $this->actingAs($editor)->put('/admin/content/news/'.$news->id, [
            'title' => 'Previewed title', 'summary' => 'x', 'status' => News::PUBLISHED, 'preview' => '1',
        ])->assertOk()->assertSee('Previewed title');
        $this->actingAs($editor)->put('/admin/content/pages/'.$page->id, [
            'title' => 'Previewed page', 'body' => 'New text', 'preview' => '1',
        ])->assertOk()->assertSee('Previewed page');

        $this->assertSame('Saved title', $news->fresh()->title);
        $this->assertSame(News::DRAFT, $news->fresh()->status);
        $this->assertSame('Imprint', $page->fresh()->title);
    }

    public function test_previewed_text_does_not_carry_over_to_the_next_form(): void
    {
        $editor = $this->editor();
        $other = $this->makeNews(['title' => 'Another post']);

        $this->actingAs($editor)->post('/admin/content/news', [
            'title' => 'Previewed only', 'summary' => 'x', 'status' => News::DRAFT, 'preview' => '1',
        ])->assertOk();

        $this->actingAs($editor)->get('/admin/content/news/'.$other->id)
            ->assertSee('value="Another post"', false)
            ->assertDontSee('Previewed only');
    }

    public function test_images_from_other_websites_are_refused_and_never_shown(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)->post('/admin/content/news', [
            'title' => 'x', 'summary' => 'x', 'status' => News::DRAFT, 'body' => '![pixel](https://tracker.example/p.png)',
        ])->assertSessionHasErrors(['body' => 'Images from other websites cannot be shown. Use the image field instead.']);
        $this->actingAs($editor)->post('/admin/content/pages', [
            'title' => 'x', 'slug' => 'x', 'body' => '![pixel](//tracker.example/p.png)',
        ])->assertSessionHasErrors('body');

        // Even if one is in the database already, it is not shown.
        $html = (string) $this->makeNews(['body' => '![A pixel](https://tracker.example/p.png) and ![Our logo](/img/logo.png)'])->bodyHtml();
        $this->assertStringNotContainsString('tracker.example', $html);
        $this->assertStringContainsString('A pixel', $html);
        $this->assertStringContainsString('<img src="/img/logo.png"', $html);
    }

    public function test_a_scheduled_post_is_labelled_and_its_image_hidden(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('news-images/a.png', 'image');
        $news = $this->makeNews(['title' => 'Next week', 'status' => News::PUBLISHED, 'published_at' => now()->addWeek()]);
        $news->forceFill(['image_path' => 'news-images/a.png', 'image_alt' => 'x'])->save();

        $this->actingAs($this->editor())->get('/admin/content')->assertSee('Scheduled, '.now()->addWeek()->format('j M Y'));

        auth()->logout();
        $this->get('/news/'.$news->slug.'/image')->assertNotFound();
        $this->actingAs(User::factory()->withRoles(Role::DatabaseLead)->create())->get('/news/'.$news->slug.'/image')->assertNotFound();
    }

    public function test_required_fields_are_enforced(): void
    {
        $this->actingAs($this->editor())->from('/admin/content/news/new')->post('/admin/content/news', ['status' => 'draft'])
            ->assertSessionHasErrors(['title' => 'Enter a title.', 'summary' => 'Enter a short summary.']);

        $this->actingAs($this->editor())->post('/admin/content/news', ['title' => 'x', 'summary' => 'x', 'status' => 'live'])
            ->assertSessionHasErrors('status');

        $this->actingAs($this->editor())->post('/admin/content/pages', ['slug' => 'x'])
            ->assertSessionHasErrors(['title' => 'Enter a title.', 'body' => 'Enter the text of the page.']);
    }

    public function test_an_image_is_stored_privately_and_shown_only_when_the_post_is_public(): void
    {
        Storage::fake('local');
        $editor = $this->editor();

        $this->actingAs($editor)->post('/admin/content/news', [
            'title' => 'With image', 'summary' => 'x', 'status' => News::DRAFT,
            'image' => UploadedFile::fake()->image('photo.jpg', 800, 600),
        ])->assertSessionHasErrors(['image_alt' => 'Describe the image in a few words.']);

        $this->actingAs($editor)->post('/admin/content/news', [
            'title' => 'With image', 'summary' => 'x', 'status' => News::DRAFT,
            'image' => UploadedFile::fake()->image('photo.jpg', 800, 600), 'image_alt' => 'The conference hall',
        ])->assertRedirect('/admin/content');

        $news = News::firstOrFail();
        Storage::disk('local')->assertExists($news->image_path);
        $this->assertStringStartsWith('news-images/', $news->image_path);

        auth()->logout();
        $this->get('/news/'.$news->slug.'/image')->assertNotFound();
        $this->actingAs(User::factory()->create())->get('/news/'.$news->slug.'/image')->assertNotFound();
        $this->actingAs($editor)->get('/news/'.$news->slug.'/image')->assertOk();

        $news->forceFill(['status' => News::PUBLISHED, 'published_at' => now()->subMinute()])->save();
        auth()->logout();
        $this->get('/news/'.$news->slug.'/image')->assertOk();
    }

    public function test_only_images_are_accepted(): void
    {
        Storage::fake('local');

        $this->actingAs($this->editor())->post('/admin/content/news', [
            'title' => 'x', 'summary' => 'x', 'status' => News::DRAFT, 'image_alt' => 'x',
            'image' => UploadedFile::fake()->create('evil.php', 10, 'application/x-php'),
        ])->assertSessionHasErrors(['image' => 'Choose a JPG, PNG or WebP image.']);

        $this->assertDatabaseCount('news', 0);
    }

    public function test_replacing_an_image_deletes_the_old_file_and_a_kept_image_needs_its_description(): void
    {
        Storage::fake('local');
        $editor = $this->editor();
        $this->actingAs($editor)->post('/admin/content/news', [
            'title' => 'x', 'summary' => 'x', 'status' => News::DRAFT,
            'image' => UploadedFile::fake()->image('a.png'), 'image_alt' => 'First picture',
        ]);
        $news = News::firstOrFail();
        $old = $news->image_path;

        $this->actingAs($editor)->put('/admin/content/news/'.$news->id, [
            'title' => 'x', 'summary' => 'x', 'status' => News::DRAFT, 'image_alt' => '',
        ])->assertSessionHasErrors(['image_alt' => 'Describe the image in a few words.']);

        $this->actingAs($editor)->put('/admin/content/news/'.$news->id, [
            'title' => 'x', 'summary' => 'x', 'status' => News::DRAFT,
            'image' => UploadedFile::fake()->image('b.png'), 'image_alt' => 'Second picture',
        ])->assertRedirect('/admin/content');

        $news->refresh();
        Storage::disk('local')->assertMissing($old);
        Storage::disk('local')->assertExists($news->image_path);
        $this->assertNotSame($old, $news->image_path);
        $this->assertSame('Second picture', $news->image_alt);
    }

    public function test_an_editor_removes_an_image(): void
    {
        Storage::fake('local');
        $editor = $this->editor();
        $this->actingAs($editor)->post('/admin/content/news', [
            'title' => 'x', 'summary' => 'x', 'status' => News::DRAFT,
            'image' => UploadedFile::fake()->image('a.png'), 'image_alt' => 'A picture',
        ]);
        $news = News::firstOrFail();
        $path = $news->image_path;

        $this->actingAs($editor)->put('/admin/content/news/'.$news->id, [
            'title' => 'x', 'summary' => 'x', 'status' => News::DRAFT, 'remove_image' => '1',
        ])->assertRedirect('/admin/content');

        Storage::disk('local')->assertMissing($path);
        $this->assertNull($news->fresh()->image_path);
        $this->assertNull($news->fresh()->image_alt);
    }

    public function test_an_editor_creates_and_changes_a_page_but_not_its_address(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)->post('/admin/content/pages', [
            'title' => 'Code of Conduct', 'slug' => 'code-of-conduct', 'body' => '## Our values',
        ])->assertRedirect('/admin/content');

        $page = Page::where('slug', 'code-of-conduct')->firstOrFail();

        $this->actingAs($editor)->put('/admin/content/pages/'.$page->id, [
            'title' => 'Code of Conduct', 'slug' => 'something-else', 'body' => '## Our shared values',
        ])->assertRedirect('/admin/content');

        $page->refresh();
        $this->assertSame('code-of-conduct', $page->slug);
        $this->assertSame('## Our shared values', $page->body);
    }

    public function test_page_addresses_are_checked(): void
    {
        $editor = $this->editor();
        $this->makePage();

        $this->actingAs($editor)->post('/admin/content/pages', ['title' => 'x', 'slug' => 'Not Valid!', 'body' => 'x'])
            ->assertSessionHasErrors(['slug' => 'Use small letters, digits and hyphens only, for example code-of-conduct.']);
        $this->actingAs($editor)->post('/admin/content/pages', ['title' => 'x', 'slug' => 'imprint', 'body' => 'x'])
            ->assertSessionHasErrors(['slug' => 'Another page already uses this address.']);
    }

    public function test_the_content_is_seeded_from_the_mockup_without_overwriting_edits(): void
    {
        $this->seed(ContentSeeder::class);

        $this->assertSame(10, News::published()->count());
        $this->assertSame(2, Page::count());
        $this->assertTrue(News::where('slug', 'get-involved-with-the-esports-research-network-ern-board')->exists());
        $this->assertStringContainsString('## Contribution formats', Page::where('slug', 'ernc27-call-for-abstracts')->value('body'));
        $this->assertStringContainsString('| Format | What it is | Submit |', Page::where('slug', 'ernc27-call-for-abstracts')->value('body'));

        News::first()->update(['title' => 'Changed by an editor']);
        $this->seed(ContentSeeder::class);

        $this->assertSame(10, News::count());
        $this->assertTrue(News::where('title', 'Changed by an editor')->exists());
    }

    public function test_the_content_seeder_may_run_on_the_live_site(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('db:seed', ['--class' => ContentSeeder::class, '--force' => true])->assertSuccessful();

        $this->assertSame(10, News::count());
    }
}
