<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemValue;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The admin area frame (R0-08). Every route sits in the `admin` middleware group
 * (signed in, a role beyond member); each tab checks its own gates on top.
 */
class AdminAreaController extends Controller
{
    /**
     * The tabs in mockup order: key => [label, gates of which one must pass].
     * Conference is open to the conference team, reviewers and the final editor.
     *
     * @var array<string, array{string, list<string>}>
     */
    public const TABS = [
        'overview' => ['Overview', ['open-admin-area']],
        'members' => ['Members', ['manage-members']],
        'conference' => ['Conference', ['manage-conference', 'review-submissions', 'decide-submissions']],
        'database' => ['Database', ['manage-database']],
        'teaching' => ['Teaching', ['manage-teaching']],
        'content' => ['Content', ['edit-content']],
        'system' => ['System', ['administer']],
    ];

    public function overview(): View
    {
        return $this->tab('overview');
    }

    public function show(string $tab): View
    {
        abort_unless(array_key_exists($tab, self::TABS) && $tab !== 'overview', 404);

        return $this->tab($tab);
    }

    private function tab(string $tab): View
    {
        abort_unless(Gate::any(self::TABS[$tab][1]), 403);

        $tabs = array_filter(self::TABS, fn (array $definition) => Gate::any($definition[1]));

        $data = ['tab' => $tab, 'label' => self::TABS[$tab][0], 'tabs' => $tabs];

        if ($tab === 'system') {
            $data['version'] = (string) config('ern.version');
            $data['environment'] = app()->environment();
            $data['lastScheduledRun'] = SystemValue::time(SystemValue::LAST_SCHEDULED_RUN);
        }

        return view('admin.tab', $data);
    }
}
