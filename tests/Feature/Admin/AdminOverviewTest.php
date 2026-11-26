<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\MembershipApplication;
use App\Models\News;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOverviewTest extends TestCase
{
    use RefreshDatabase;

    private function draftNews(int $count): void
    {
        foreach (range(1, $count) as $n) {
            $news = new News(['title' => "Draft {$n}", 'summary' => 'x', 'status' => News::DRAFT]);
            $news->slug = 'draft-'.$n;
            $news->save();
        }
    }

    public function test_an_admin_sees_every_queue_with_the_right_counts(): void
    {
        MembershipApplication::factory()->count(3)->create();
        MembershipApplication::factory()->create(['status' => MembershipApplication::DECLINED]);
        User::factory()->create()->forceFill(['membership_end_requested_at' => now()])->save();
        $this->draftNews(2);
        $admin = User::factory()->withRoles(Role::Admin)->create();

        $this->actingAs($admin)->get('/admin')->assertOk()
            ->assertSee('Needs attention')
            ->assertSee('3 membership applications wait for the board')
            ->assertSee('Owner: Board')
            ->assertSee('Confirm or decline')
            ->assertSee('1 member asks to end the membership')
            ->assertSee('2 news posts are still a draft')
            ->assertSee('Applications to confirm')
            ->assertSee('<b>3</b><span>Applications to confirm</span>', false)
            ->assertSee('<b>2</b><span>Members</span>', false)
            ->assertSee('<b>2</b><span>Drafts</span>', false)
            ->assertSee('Who sees what')
            ->assertSee('Concerns only. They never appear in this area.');
    }

    public function test_an_editor_sees_only_the_content_queues(): void
    {
        MembershipApplication::factory()->count(3)->create();
        User::factory()->create()->forceFill(['membership_end_requested_at' => now()])->save();
        $this->draftNews(1);
        $notice = new Notice(['kind' => 'x', 'title' => 'x', 'body' => 'x', 'status' => Notice::DRAFT]);
        $notice->save();

        $this->actingAs(User::factory()->withRoles(Role::Editor)->create())->get('/admin')->assertOk()
            ->assertSee('1 news post is still a draft')
            ->assertSee('1 notice is still a draft')
            ->assertSee('<b>2</b><span>Drafts</span>', false)
            ->assertDontSee('membership application')
            ->assertDontSee('end the membership')
            ->assertDontSee('Applications to confirm')
            ->assertDontSee('<span>Members</span>', false);
    }

    public function test_a_reviewer_sees_no_queue_and_no_counts_of_other_areas(): void
    {
        MembershipApplication::factory()->count(2)->create();
        $this->draftNews(2);
        (new Notice(['kind' => 'x', 'title' => 'x', 'body' => 'x', 'status' => Notice::DRAFT]))->save();
        User::factory()->create()->forceFill(['membership_end_requested_at' => now()])->save();

        $this->actingAs(User::factory()->withRoles(Role::Reviewer)->create())->get('/admin')->assertOk()
            ->assertDontSee('end the membership')
            ->assertSee('Nothing waits for you at the moment.')
            ->assertDontSee('membership application')
            ->assertDontSee('still a draft')
            ->assertDontSee('x-thing', false);
    }

    public function test_the_counts_follow_the_data(): void
    {
        $admin = User::factory()->withRoles(Role::Admin)->create();
        $this->actingAs($admin)->get('/admin')->assertSee('Nothing waits for you at the moment.')->assertSee('<b>0</b><span>Applications to confirm</span>', false);

        MembershipApplication::factory()->create();
        $this->draftNews(1);
        $this->actingAs($admin)->get('/admin')
            ->assertSee('1 membership application waits for the board')
            ->assertSee('<b>1</b><span>Drafts</span>', false);
    }

    public function test_members_and_guests_are_kept_out(): void
    {
        $this->get('/admin')->assertRedirect('/members/sign-in');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }
}
