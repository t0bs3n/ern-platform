<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageFrameTest extends TestCase
{
    private const NAVIGATION = ['News', 'Conferences', 'Database', 'Live', 'Teaching', 'Podcast', 'About Us', 'Get Involved'];

    public function test_the_start_page_shows_the_frame_with_every_navigation_label(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        foreach (self::NAVIGATION as $label) {
            $response->assertSee('>'.$label.'</a>', false);
        }
        $response->assertSee('Skip to content');
        $response->assertSee('Saved library');
        $response->assertSee('Member area');
        $response->assertSee('id="mobile-menu"', false);
        $response->assertSee('href="#mobile-menu"', false);
        $response->assertSee('class="site-footer"', false);
        $response->assertSee('board@esportsresearch.net');
        $response->assertSee('Imprint');
    }

    public function test_the_frame_has_no_mockup_parts_and_no_admin_link_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertDontSee('x-mockbar', false);
        $response->assertDontSee('Ask Ernie');
        $response->assertDontSee('About this mockup');
        $response->assertDontSee('Admin area');
    }

    public function test_the_frame_loads_nothing_from_other_sites(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertDoesNotMatchRegularExpression('/<(script|link|img|iframe)[^>]+(src|href)="(https?:)?\/\/(?!localhost)/i', $html);
    }

    public function test_the_footer_shows_the_social_profiles(): void
    {
        $response = $this->get('/');

        foreach (config('ern.social') as $profile) {
            $response->assertSee('aria-label="'.$profile['label'].'"', false);
        }
    }

    public function test_the_current_navigation_item_is_marked(): void
    {
        $html = view('layouts.site', ['nav' => 'news'])->render();

        $this->assertSame(2, substr_count($html, 'aria-current="page"'));
        $this->assertMatchesRegularExpression('/<li class="current-menu-item"><a href="[^"]*\/news" aria-current="page">News<\/a>/', $html);
    }

    public function test_an_unknown_address_answers_404_inside_the_frame(): void
    {
        $response = $this->get('/this-page-does-not-exist');

        $response->assertNotFound();
        $response->assertSee('Page not found');
        $response->assertSee('class="site-header"', false);
        $response->assertSee('class="site-footer"', false);
        $response->assertSee('Back to the home page');
    }

    public function test_the_stylesheet_and_script_exist_without_external_fonts(): void
    {
        $css = file_get_contents(public_path('css/ern.css'));

        $this->assertStringNotContainsString('fonts.googleapis.com', $css);
        $this->assertStringNotContainsString('@import', $css);
        $this->assertFileExists(public_path('js/ern.js'));
        $this->assertFileExists(public_path('img/logo.png'));
    }
}
