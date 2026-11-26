<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Mail\WelcomeMember;
use App\Members\ActivateMember;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * The link in the welcome e-mail (R1-11): a new member sets a password and is signed in.
 * The link is checked here, so that an expired, used or broken link gets a friendly page.
 */
class ActivationController extends Controller
{
    use PasswordValidationRules;

    public function show(Request $request, int $user): Response
    {
        [$state, $member] = $this->check($request, $user);

        if ($state !== 'ok') {
            return $this->problem($state, $user, $request);
        }

        return response()->view('members.activate', ['action' => $request->fullUrl()]);
    }

    public function store(Request $request, int $user, ActivateMember $activate): Response|RedirectResponse
    {
        [$state] = $this->check($request, $user);

        if ($state !== 'ok') {
            return $this->problem($state, $user, $request);
        }

        $validator = Validator::make($request->only('password', 'password_confirmation'), [
            'password' => $this->passwordRules(),
        ], $this->passwordMessages());

        if ($validator->fails()) {
            return redirect()->to($request->fullUrl())->withErrors($validator);
        }

        $member = $activate($user, (string) $request->input('password'));

        if ($member === null) {
            // Something changed since the check (a password was set, or the approval withdrawn): look again.
            [$state] = $this->check($request, $user);

            return $this->problem($state === 'ok' ? 'used' : $state, $user, $request);
        }

        Auth::login($member);
        $request->session()->regenerate();

        return redirect()->route('members')->with('status', 'Your account is active. Welcome to the member area.');
    }

    /**
     * "Send a new link" from the expired page. The answer is always the same page.
     * Nobody types an address: the e-mail only goes to the member whose signed link was used.
     */
    public function resend(Request $request, int $user): Response
    {
        $link = URL::route('members.activate', ['user' => $user]).'?'.http_build_query([
            'expires' => (string) $request->input('expires'),
            'signature' => (string) $request->input('signature'),
        ]);

        if (! URL::hasCorrectSignature(Request::create($link))) {
            return $this->problem('invalid', $user, $request);
        }

        $keys = ['activation-link-user:'.$user => [3, 86400], 'activation-link-ip:'.$request->ip() => [10, 3600]];
        $limited = collect($keys)->contains(fn ($limit, $key) => RateLimiter::tooManyAttempts($key, $limit[0]));

        if (! $limited) {
            foreach ($keys as $key => [, $decay]) {
                RateLimiter::hit($key, $decay);
            }

            $member = User::find($user);
            if ($member !== null && $member->isApproved() && $member->password === null) {
                try {
                    Mail::to($member->email)->send(new WelcomeMember($member));
                } catch (Throwable $e) {
                    Log::warning('A new activation link could not be sent.', ['member' => $member->id, 'error' => $e::class]);
                }
            }
        }

        return response()->view('members.activate-problem', ['state' => 'sent'], 200);
    }

    /**
     * @return array{string, ?User} the state (ok, invalid, expired, used) and the member
     */
    private function check(Request $request, int $userId): array
    {
        $member = User::find($userId);

        if (! URL::hasCorrectSignature($request) || $member === null || ! $member->isApproved()) {
            return ['invalid', null];
        }
        if (! URL::signatureHasNotExpired($request)) {
            return ['expired', $member];
        }
        if ($member->password !== null) {
            return ['used', $member];
        }

        return ['ok', $member];
    }

    private function problem(string $state, int $user, Request $request): Response
    {
        $status = ['invalid' => 403, 'expired' => 410, 'used' => 410][$state] ?? 200;

        return response()->view('members.activate-problem', [
            'state' => $state,
            'resend' => $state === 'expired' ? [
                'action' => route('members.activate.resend', ['user' => $user]),
                'expires' => (string) $request->query('expires'),
                'signature' => (string) $request->query('signature'),
            ] : null,
        ], $status);
    }
}
