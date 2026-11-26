<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Page;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * The public ERN content from the mockup (docs/mockup/data.json: `news` and `pages`),
 * kept as files in database/seeders/content/. It holds no personal data beyond public
 * author names, so it may run on staging and live too: `php artisan db:seed --class=ContentSeeder`.
 * It only adds what is missing and never overwrites an editor's changes.
 */
class ContentSeeder extends Seeder
{
    /** @var array<string, array{string, string}> slug => [title, file] */
    private const PAGES = [
        'ernc27-call-for-abstracts' => ['ERNC27 Call for Abstracts', 'cfa.md'],
        'ernc26-summary' => ['ERNC26: Conference summary', 'ernc26.md'],
        // Code of Conduct, Imprint and Privacy Policy (R1-05).
        'code-of-conduct' => ['Code of Conduct', 'code-of-conduct.md'],
        'imprint' => ['Imprint', 'imprint.md'],
        'privacy' => ['Privacy Policy', 'privacy.md'],
        // Text blocks of About Us and Get Involved (R1-04).
        'about-mission' => ['About Us: Mission Statement', 'about-mission.md'],
        'about-history' => ['About Us: Our History', 'about-history.md'],
        'about-former-board' => ['About Us: Former ERN Board Members', 'about-former-board.md'],
        'get-involved-community' => ['Get Involved: A Community-Led Network', 'get-involved-community.md'],
        'get-involved-start' => ['Get Involved: How to Get Started', 'get-involved-start.md'],
    ];

    public function run(): void
    {
        foreach (self::PAGES as $slug => [$title, $file]) {
            if (Page::where('slug', $slug)->exists()) {
                continue;
            }

            $page = new Page(['title' => $title, 'body' => file_get_contents(__DIR__.'/content/'.$file)]);
            $page->slug = $slug;
            $page->save();
        }

        if (! TeamMember::exists()) {
            $team = json_decode(file_get_contents(__DIR__.'/content/team.json'), true, flags: JSON_THROW_ON_ERROR);

            foreach ($team as $group => $people) {
                foreach ($people as $position => [$name, $affiliation, $role]) {
                    TeamMember::create(['group' => $group, 'name' => $name, 'affiliation' => $affiliation, 'role_title' => $role, 'position' => ($position + 1) * 10]);
                }
            }
        }

        $posts = json_decode(file_get_contents(__DIR__.'/content/news.json'), true, flags: JSON_THROW_ON_ERROR);

        foreach ($posts as $post) {
            if (News::where('slug', $post['slug'])->exists()) {
                continue;
            }

            $news = new News([
                'title' => $post['title'],
                'category' => $post['category'],
                'summary' => $post['summary'],
                'body' => $post['summary'],
                'status' => News::PUBLISHED,
                'published_at' => Carbon::parse($post['published_at'], 'UTC')->setTime(9, 0),
            ]);
            $news->slug = $post['slug'];
            $news->save();
        }
    }
}
