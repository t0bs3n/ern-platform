<?php

namespace Tests\Feature;

use App\Members\ActivationLink;
use App\Models\Conference;
use App\Models\News;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Database\Seeders\EventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * R1-20: every public address answers 200, has exactly one h1 and a page title.
 */
class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    private const FIXED = [
        '/', '/news', '/about', '/get-involved', '/join', '/conferences', '/events',
        '/podcast', '/live', '/code-of-conduct', '/imprint', '/privacy',
        '/members/sign-in', '/members/forgot-password', '/members/reset-password/some-token',
    ];

    /** @return list<string> */
    private function addresses(): array
    {
        $this->seed([ContentSeeder::class, EventSeeder::class]);

        $addresses = self::FIXED;
        foreach (News::published()->pluck('slug') as $slug) {
            $addresses[] = '/news/'.$slug;
        }
        foreach (Conference::all() as $conference) {
            $addresses[] = '/conferences/'.$conference->slug;
            $addresses[] = '/conferences/'.$conference->slug.'/'.($conference->status === Conference::UPCOMING ? 'call' : 'summary');
        }

        return $addresses;
    }

    private function assertOneHeadingAndTitle(TestResponse $response, string $address): void
    {
        $html = (string) $response->getContent();

        $this->assertSame(1, preg_match_all('/<h1[\s>]/i', $html), $address.' should have exactly one h1');
        $this->assertSame(1, preg_match('#<title>\s*([^<]*\S)\s*</title>#', $html), $address.' should have a page title');
        $this->assertSame(1, preg_match('#<html[^>]* lang="en"#', $html), $address.' should declare its language');
    }

    public function test_every_public_address_answers_200_with_one_h1_and_a_title(): void
    {
        $addresses = $this->addresses();
        $this->assertGreaterThan(count(self::FIXED) + 5, count($addresses));

        foreach ($addresses as $address) {
            $response = $this->get($address);
            $this->assertSame(200, $response->status(), $address.' should answer 200');
            $this->assertOneHeadingAndTitle($response, $address);
        }
    }

    public function test_public_pages_use_us_spelling(): void
    {
        // docs/DECISIONS.md: US spelling everywhere. Proper names such as "... Centre (MMTC)" are allowed.
        $uk = '/\b(organis(e|ed|es|ing|ation|ations|er|ers)|programmes?|behaviours?|colours?|favour(ite)?s?|honour|labour|licence|defence|recognis(e|ed|es|ing)|catalogue|travell(ed|ing)|cancell(ed|ing)|enrolment|analys(e|ed|es|ing)|whilst|amongst)\b/i';

        foreach ($this->addresses() as $address) {
            $text = strip_tags((string) $this->get($address)->getContent());

            $this->assertDoesNotMatchRegularExpression($uk, $text, $address);
        }
    }

    public function test_no_public_page_names_the_person_behind_the_ombudsperson_role(): void
    {
        foreach ($this->addresses() as $address) {
            $text = strip_tags((string) $this->get($address)->getContent());

            // The role may be mentioned ("The ERN Ombudsperson reads every report"), never next to a name.
            $this->assertDoesNotMatchRegularExpression('/[A-Z][a-z]+\s*\((?:ERN )?Ombudsperson\)|Ombudsperson[:,]?\s+[A-Z][a-z]+ [A-Z][a-z]+/', $text, $address);
        }
    }

    public function test_page_titles_differ_between_pages(): void
    {
        $titles = [];
        foreach ($this->addresses() as $address) {
            preg_match('#<title>\s*([^<]*\S)\s*</title>#', (string) $this->get($address)->getContent(), $match);
            $titles[$address] = $match[1] ?? '';
        }

        // Pages of one step share a title: the tabs of a conference, and the two halves of
        // "Set a new password" (asking for the link and using it). All others have their own.
        $groups = [];
        foreach ($titles as $address => $title) {
            $this->assertNotSame('', $title, $address);
            $group = preg_replace(['#/(call|summary)$#', '#^/members/reset-password/.*$#'], ['', '/members/forgot-password'], $address);
            $groups[$title][$group] = true;
        }
        foreach ($groups as $title => $members) {
            $this->assertCount(1, $members, 'Pages share the title "'.$title.'": '.implode(', ', array_keys($members)));
        }
    }

    public function test_the_pages_after_joining_and_from_the_welcome_link_have_one_h1_and_a_title(): void
    {
        $this->assertOneHeadingAndTitle($this->withSession(['joined' => 'Ada'])->get('/join')->assertOk()->assertSee('Thank you, Ada'), '/join after sending');

        $link = ActivationLink::for(User::factory()->withoutPassword()->create());
        $this->assertOneHeadingAndTitle($this->get($link)->assertOk(), 'activation');
        $this->assertOneHeadingAndTitle($this->get($link.'x'), 'broken activation');
    }

    public function test_the_not_found_page_has_one_h1_and_a_title(): void
    {
        $response = $this->get('/no-such-page');

        $response->assertNotFound();
        $this->assertOneHeadingAndTitle($response, '/no-such-page');
    }
}
