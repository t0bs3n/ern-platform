<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ApplicationDeclined;
use App\Mail\WelcomeMember;
use App\Members\ApproveApplication;
use App\Members\DecisionException;
use App\Members\DeclineApplication;
use App\Models\MembershipApplication;
use App\Models\MembershipDecision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

/** The Members tab (R1-10): open applications, approve or decline. Routes carry can:manage-members. */
class MembersAdminController extends Controller
{
    public function index(): View
    {
        $since = now()->subDays(MembershipApplication::KEEP_DECLINED_DAYS);

        return view('admin.members.index', [
            'open' => MembershipApplication::where('status', MembershipApplication::OPEN)->latest()->get(),
            'declined' => MembershipApplication::where('status', MembershipApplication::DECLINED)->where('decided_at', '>=', $since)->latest('decided_at')->get(),
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
}
