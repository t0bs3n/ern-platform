<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\Conference;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * R1-20: an error in a group of choices is shown and tied to its group.
 */
class FormErrorsTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string, string, string}> */
    public static function choiceGroups(): array
    {
        return [
            'news status' => ['/admin/content/news/new', '/admin/content/news', 'status'],
            'notice status' => ['/admin/content/notices/new', '/admin/content/notices', 'status'],
            'team group' => ['/admin/content/people/new', '/admin/content/people', 'group'],
            'event kind' => ['/admin/content/events/new', '/admin/content/events', 'kind'],
        ];
    }

    #[DataProvider('choiceGroups')]
    public function test_a_wrong_choice_is_shown_and_tied_to_its_group(string $form, string $action, string $field): void
    {
        $admin = User::factory()->withRoles(Role::Admin)->create();

        $this->actingAs($admin)->get($form)->assertOk()->assertDontSee('f-'.$field.'-err', false);
        $this->actingAs($admin)->from($form)->post($action, [$field => 'not-a-choice'])->assertRedirect($form)->assertSessionHasErrors($field);

        $this->actingAs($admin)->from($form)->followingRedirects()->post($action, [$field => 'not-a-choice'])->assertOk()
            ->assertSee('aria-describedby="f-'.$field.'-err"', false)
            ->assertSee('<span class="x-err" id="f-'.$field.'-err">', false);
    }

    public function test_conference_status_and_key_date_errors_are_shown_and_tied(): void
    {
        $this->seed(ContentSeeder::class);
        $admin = User::factory()->withRoles(Role::Admin)->create();
        $conference = Conference::firstOrFail();
        $form = route('admin.conference.edit', $conference);
        $rows = array_fill(0, 13, ['label' => 'Deadline', 'date' => '2027-01-01']);

        $this->actingAs($admin)->get($form)->assertOk()->assertDontSee('f-status-err', false)->assertDontSee('f-kd-err', false);
        $this->actingAs($admin)->from($form)->followingRedirects()
            ->put(route('admin.conference.update', $conference), ['status' => 'not-a-choice', 'key_dates' => $rows])
            ->assertOk()
            ->assertSee('aria-describedby="f-status-err"', false)
            ->assertSee('<span class="x-err" id="f-status-err">', false)
            ->assertSee('aria-describedby="f-kd-err"', false)
            ->assertSee('<span class="x-err" id="f-kd-err">', false);
    }

    public function test_a_refused_role_change_is_tied_to_the_roles(): void
    {
        $admin = User::factory()->withRoles(Role::Admin)->create();
        $member = User::factory()->create();
        $form = route('admin.members.show', $member);

        $this->actingAs($admin)->get($form)->assertOk()->assertDontSee('f-roles-err', false);
        $this->actingAs($admin)->from($form)->followingRedirects()
            ->put(route('admin.members.roles', $member), ['roles' => ['not-a-role']])
            ->assertOk()
            ->assertSee('aria-describedby="f-roles-err"', false)
            ->assertSee('id="f-roles-err" role="alert"', false);
    }
}
