<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Only the session cookie is set (docs/DECISIONS.md), and every answer carries the protective headers.
 */
class CookiesAndHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_page_with_a_form_sets_only_the_session_cookie(): void
    {
        foreach (['/', '/join', '/members/sign-in'] as $address) {
            $cookies = array_map(fn ($cookie) => $cookie->getName(), $this->get($address)->headers->getCookies());

            $this->assertSame([config('session.cookie')], $cookies, $address);
        }
    }

    public function test_forms_still_need_their_csrf_token(): void
    {
        $this->get('/members/sign-in')->assertSee('name="_token"', false);

        // The test run switches the check off; switch it back on for this request.
        $this->app->instance('env', 'staging');
        $this->post('/members/sign-in', ['email' => 'a@example.test', 'password' => 'x'])->assertStatus(419);
    }

    public function test_every_answer_has_the_protective_headers(): void
    {
        foreach (['/', '/members/sign-in', '/no-such-page'] as $address) {
            $this->get($address)
                ->assertHeader('X-Frame-Options', 'DENY')
                ->assertHeader('X-Content-Type-Options', 'nosniff')
                ->assertHeader('Referrer-Policy', 'same-origin')
                ->assertHeaderMissing('Strict-Transport-Security');
        }
    }
}
