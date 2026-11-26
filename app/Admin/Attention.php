<?php

namespace App\Admin;

use App\Models\MembershipApplication;
use App\Models\News;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * "Needs attention" on the admin overview (R1-16): the open queues, each shown only to
 * the people whose roles cover it. Later releases add their queues here.
 */
final class Attention
{
    /**
     * @return list<array{text: string, owner: string, action: string, url: string}>
     */
    public function forCurrentUser(): array
    {
        $items = [];

        if (Gate::allows('manage-members')) {
            $open = MembershipApplication::where('status', MembershipApplication::OPEN)->count();
            if ($open > 0) {
                $items[] = ['text' => $open.' membership application'.($open > 1 ? 's wait' : ' waits').' for the board', 'owner' => 'Board', 'action' => 'Confirm or decline', 'url' => route('admin.members.index')];
            }
            $leaving = User::whereNotNull('approved_at')->whereNotNull('membership_end_requested_at')->count();
            if ($leaving > 0) {
                $items[] = ['text' => $leaving.' member'.($leaving > 1 ? 's ask' : ' asks').' to end the membership', 'owner' => 'Board', 'action' => 'See the member list', 'url' => route('admin.members.index')];
            }
        }

        if (Gate::allows('edit-content')) {
            $drafts = News::where('status', News::DRAFT)->count();
            if ($drafts > 0) {
                $items[] = ['text' => $drafts.' news '.($drafts > 1 ? 'posts are' : 'post is').' still a draft', 'owner' => 'Editor', 'action' => 'Open the content', 'url' => route('admin.tab', 'content')];
            }
            $noticeDrafts = Notice::where('status', Notice::DRAFT)->count();
            if ($noticeDrafts > 0) {
                $items[] = ['text' => $noticeDrafts.' '.($noticeDrafts > 1 ? 'notices are' : 'notice is').' still a draft', 'owner' => 'Editor', 'action' => 'Open the content', 'url' => route('admin.tab', 'content')];
            }
        }

        return $items;
    }

    /**
     * The number tiles, each only for the roles it belongs to.
     *
     * @return list<array{count: int, label: string, url: string}>
     */
    public function tilesForCurrentUser(): array
    {
        $tiles = [];
        if (Gate::allows('manage-members')) {
            $tiles[] = ['count' => MembershipApplication::where('status', MembershipApplication::OPEN)->count(), 'label' => 'Applications to confirm', 'url' => route('admin.members.index')];
            $tiles[] = ['count' => User::whereNotNull('approved_at')->count(), 'label' => 'Members', 'url' => route('admin.members.index')];
        }
        if (Gate::allows('edit-content')) {
            $tiles[] = ['count' => News::where('status', News::DRAFT)->count() + Notice::where('status', Notice::DRAFT)->count(), 'label' => 'Drafts', 'url' => route('admin.tab', 'content')];
        }

        return $tiles;
    }
}
