<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinRequest;
use App\Mail\ApplicationReceived;
use App\Models\MembershipApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

/**
 * Become a member (R1-09). Every valid send gets the same answer, whether the address is new,
 * belongs to a member, has an open application, or a robot filled the hidden field.
 */
class JoinController extends Controller
{
    public function show(): View
    {
        return view('about.join', ['joined' => session('joined')]);
    }

    public function store(JoinRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $robot = filled($data['website'] ?? null);

        if (! $robot && ! MembershipApplication::addressIsTaken($data['email'])) {
            $application = new MembershipApplication($data);
            $application->agreed_at = now();
            $application->status = MembershipApplication::OPEN;
            $application->save();

            // Sent after the answer, so that the answer takes as long with or without an e-mail.
            defer(function () use ($application) {
                try {
                    Mail::to($application->email)->send(new ApplicationReceived($application));
                } catch (Throwable $e) {
                    Log::warning('The confirmation of an application could not be sent.', ['application' => $application->id, 'error' => $e->getMessage()]);
                }
            });
        }

        return redirect()->route('join')->with('joined', $data['first_name']);
    }
}
