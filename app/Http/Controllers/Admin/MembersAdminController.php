<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRolesRequest;
use App\Mail\ApplicationDeclined;
use App\Mail\WelcomeMember;
use App\Members\ApproveApplication;
use App\Members\ChangeRoles;
use App\Members\DecisionException;
use App\Members\DeclineApplication;
use App\Members\MemberListCsv;
use App\Models\MemberAdminLog;
use App\Models\MembershipApplication;
use App\Models\MembershipDecision;
use App\Models\User;
use App\Support\Like;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/** The Members tab (R1-10): open applications, approve or decline. Routes carry can:manage-members. */
class MembersAdminController extends Controller
{
    public function index(Request $request): View
    {
        $q = Str::limit(trim((string) $request->query('q')), 100, '');
        $members = User::whereNotNull('approved_at')->with('roles');
        foreach (preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $like = Like::contains($word);
            $members->where(function ($query) use ($like) {
                foreach (['first_name', 'last_name', 'country', 'institution'] as $column) {
                    $query->orWhereRaw($column." LIKE ? ESCAPE '!'", [$like]);
                }
            });
        }

        $since = now()->subDays(MembershipApplication::KEEP_DECLINED_DAYS);

        return view('admin.members.index', [
            'open' => MembershipApplication::where('status', MembershipApplication::OPEN)->latest()->get(),
            'declined' => MembershipApplication::where('status', MembershipApplication::DECLINED)->where('decided_at', '>=', $since)->latest('decided_at')->get(),
            'q' => $q,
            'members' => $members->orderBy('last_name')->orderBy('first_name')->orderBy('id')->paginate(50)->withQueryString(),
            'memberTotal' => User::whereNotNull('approved_at')->count(),
            'approved' => MembershipDecision::with('member')->where('decision', MembershipDecision::APPROVED)->where('created_at', '>=', $since)->latest()->get(),
        ]);
    }

    public function approve(Request $request, MembershipApplication $application, ApproveApplication $approve): RedirectResponse
    {
        try {
            $member = $approve($application, $request->user());
        } catch (DecisionException $e) {
            return back()->with('status', $e->getMessage());
        }

        try {
            Mail::to($member->email)->send(new WelcomeMember($member));
            $this->markSent(['member_id' => $member->id, 'decision' => MembershipDecision::APPROVED]);
        } catch (Throwable $e) {
            Log::warning('The welcome e-mail could not be sent.', ['member' => $member->id, 'error' => $e::class]);

            return back()->with('status', 'Approved. The welcome e-mail could not be sent. The new member can use "Forgot your password?" on the sign-in page.');
        }

        return back()->with('status', 'Approved, welcome e-mail sent');
    }

    public function decline(Request $request, MembershipApplication $application, DeclineApplication $decline): RedirectResponse
    {
        try {
            $application = $decline($application, $request->user());
        } catch (DecisionException $e) {
            return back()->with('status', $e->getMessage());
        }

        try {
            Mail::to($application->email)->send(new ApplicationDeclined($application));
            $this->markSent(['application_id' => $application->id, 'decision' => MembershipDecision::DECLINED]);
        } catch (Throwable $e) {
            Log::warning('The answer to a declined application could not be sent.', ['application' => $application->id, 'error' => $e::class]);

            return back()->with('status', 'Declined. The e-mail to the applicant could not be sent.');
        }

        return back()->with('status', 'Declined.');
    }

    /** Notes on the decision that the e-mail to the applicant went out. */
    private function markSent(array $where): void
    {
        MembershipDecision::where($where)->latest('id')->first()?->forceFill(['email_sent_at' => now()])->save();
    }

    /** One member, with the rights box. Unapproved or unknown members answer 404. */
    public function show(User $member): View
    {
        abort_unless($member->isApproved(), 404);

        return view('admin.members.show', ['member' => $member->load('roles')]);
    }

    public function updateRoles(UpdateRolesRequest $request, User $member, ChangeRoles $change): RedirectResponse
    {
        abort_unless($member->isApproved(), 404);

        try {
            $result = $change($request->user(), $member, $request->roles());
        } catch (DecisionException $e) {
            return redirect()->route('admin.members.show', $member)->withErrors(['roles' => $e->getMessage()]);
        }

        $labels = $member->fresh('roles')->roles->sortBy(fn ($r) => array_search($r->role, Role::cases(), true))->map(fn ($r) => $r->role->label())->implode(', ');
        $message = 'Rights saved for '.$member->full_name.': '.$labels.'.';
        if ($result['movedFrom'] !== null) {
            $message .= ' Final editor was taken from '.$result['movedFrom']->full_name.', because one person holds this role.';
        }

        return redirect()->route('admin.members.show', $member)->with('status', $message);
    }

    /** The member list as a CSV file. Logged. */
    public function export(Request $request, MemberListCsv $csv): StreamedResponse
    {
        MemberAdminLog::write(MemberAdminLog::LIST_EXPORTED, $request->user()->id, null, null, 'rows:'.$csv->count());

        return $csv->download();
    }
}
