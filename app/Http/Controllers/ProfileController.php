<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\ProfileRequest;
use App\Mail\ConfirmNewEmail;
use App\Mail\EmailChanged;
use App\Mail\MembershipEndRequested;
use App\Members\EmailChangeLink;
use App\Models\MembershipDecision;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

/**
 * A member's own profile (R1-12). Every action works on the signed-in member only:
 * no address here carries another member's id. Routes carry auth and can:use-member-area.
 */
class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('members.profile', ['member' => $request->user()]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $member = $request->user();
        $oldName = [$member->first_name, $member->last_name];

        $member->fill($request->validated());
        $member->fill([
            'listed_in_directory' => $request->boolean('listed_in_directory'),
            'open_to_collaboration' => $request->boolean('open_to_collaboration'),
            'show_discord_in_directory' => $request->boolean('show_discord_in_directory'),
        ]);

        // A new name gets a new address, so that a former name never stays in it.
        if ($oldName !== [$member->first_name, $member->last_name]) {
            $member->slug = User::uniqueSlug($member->first_name, $member->last_name);
        }

        $member->save();

        return redirect()->route('members.profile')->with('status', 'Your details are saved.');
    }

    /**
     * Asks for a new e-mail address. The answer is the same whether or not the address can be used,
     * so that the form cannot be used to find out who is a member.
     */
    public function requestEmailChange(Request $request): RedirectResponse
    {
        $request->validate([
            'new_email' => ['required', 'string', 'max:255', 'email:rfc'],
            'current_password' => ['required', 'current_password'],
        ], [
            'new_email.required' => 'This field is needed.',
            'new_email.email' => 'Enter an e-mail address, for example you@university.edu.',
            'new_email.max' => 'This is too long.',
            'current_password.required' => 'Enter your password.',
            'current_password.current_password' => 'The password is not right.',
        ]);

        $member = $request->user();
        $new = Str::lower(trim((string) $request->input('new_email')));
        $answer = 'Check the new address. If it can be used, a link to confirm it is on its way. Your address stays as it is until you click the link.';

        // The request is noted in every case, so the profile page looks the same whether or not
        // the address is taken. A taken address simply gets no e-mail; the confirm step checks again.
        $member->forceFill(['pending_email' => $new, 'pending_email_requested_at' => now()])->save();

        if ($new === $member->email || User::where('email', $new)->whereKeyNot($member->id)->exists()) {
            return redirect()->route('members.profile')->with('status', $answer);
        }

        // Sent after the answer, so that a free and a taken address answer alike, also in time.
        defer(function () use ($member, $new) {
            try {
                Mail::to($new)->send(new ConfirmNewEmail($member));
            } catch (Throwable $e) {
                Log::warning('The confirmation of a new e-mail address could not be sent.', ['member' => $member->id, 'error' => $e::class]);
                $member->forceFill(['pending_email' => null, 'pending_email_requested_at' => null])->save();
            }
        });

        return redirect()->route('members.profile')->with('status', $answer);
    }

    public function confirmEmailChange(Request $request, int $user, string $hash): Response|RedirectResponse
    {
        $member = $request->user();
        $invalid = fn () => response()->view('members.email-link-invalid', [], 403);

        if (! URL::hasValidSignature($request) || $member->id !== $user || $member->pending_email === null
            || ! hash_equals(sha1($member->pending_email), $hash)
            || $member->pending_email_requested_at?->lt(now()->subHours(EmailChangeLink::HOURS))) {
            return $invalid();
        }

        $old = $member->email;
        $new = $member->pending_email;

        if (User::where('email', $new)->whereKeyNot($member->id)->exists()) {
            $member->forceFill(['pending_email' => null, 'pending_email_requested_at' => null])->save();

            return redirect()->route('members.profile')->with('status', 'This address cannot be used. Your e-mail address stays as it was.');
        }

        try {
            DB::transaction(function () use ($member, $old, $new) {
                $member->forceFill([
                    'email' => $new,
                    'email_verified_at' => now(),
                    'pending_email' => null,
                    'pending_email_requested_at' => null,
                ])->save();
                // Old reset links for the old address must not work any more.
                DB::table('password_reset_tokens')->whereIn('email', [$old, $new])->delete();
            });
        } catch (UniqueConstraintViolationException) {
            return redirect()->route('members.profile')->with('status', 'This address cannot be used. Your e-mail address stays as it was.');
        }

        $request->session()->regenerate();

        try {
            Mail::to($old)->send(new EmailChanged($member));
        } catch (Throwable $e) {
            Log::warning('The notice about a changed e-mail address could not be sent.', ['member' => $member->id, 'error' => $e::class]);
        }

        return redirect()->route('members.profile')->with('status', 'Your new e-mail address is saved. Use it to sign in from now on.');
    }

    /** Sends a request to end the membership to the people who manage members. Deletes nothing. */
    public function requestEnd(Request $request): RedirectResponse
    {
        $request->validate(['confirm' => ['accepted']], ['confirm.accepted' => 'Please tick the box to confirm.']);

        $member = $request->user();

        if ($member->membership_end_requested_at?->gt(now()->subDay())) {
            return redirect()->route('members.profile')->with('status', 'Your request is sent. The board will contact you. Nothing is deleted until then.');
        }

        $admins = User::whereNotNull('approved_at')
            ->whereHas('roles', fn ($query) => $query->where('role', Role::Admin->value))
            ->get();

        $sent = 0;
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new MembershipEndRequested($member));
                $sent++;
            } catch (Throwable $e) {
                Log::warning('A request to end a membership could not be sent.', ['member' => $member->id, 'error' => $e::class]);
            }
        }

        if ($sent === 0) {
            return redirect()->route('members.profile')->with('status', 'Your request could not be sent. Please try again later, or write to board@esportsresearch.net.');
        }

        $member->forceFill(['membership_end_requested_at' => now()])->save();

        return redirect()->route('members.profile')->with('status', 'Your request is sent. The board will contact you. Nothing is deleted until then.');
    }

    /** Everything the platform stores about the member, as a JSON file. Built from a fixed list of fields. */
    public function download(Request $request): JsonResponse
    {
        $member = $request->user();
        $decision = MembershipDecision::where('member_id', $member->id)->where('decision', MembershipDecision::APPROVED)->latest('id')->first();
        $time = fn ($value) => $value?->toIso8601String();

        $data = [
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'email' => $member->email,
            'pending_email' => $member->pending_email,
            'page_address' => $member->slug,
            'institution' => $member->institution,
            'country' => $member->country,
            'career_stage' => $member->career_stage,
            'research_interests' => $member->interests,
            'orcid' => $member->orcid,
            'discord_name' => $member->discord_name,
            'show_me_in_the_member_directory' => $member->listed_in_directory,
            'open_to_collaboration_requests' => $member->open_to_collaboration,
            'show_my_discord_name_in_the_member_directory' => $member->show_discord_in_directory,
            'roles' => $member->roles->map(fn ($role) => $role->role->label())->values()->all(),
            'member_since' => $time($member->approved_at),
            'email_confirmed_at' => $time($member->email_verified_at),
            'account_created_at' => $time($member->created_at),
            'last_changed_at' => $time($member->updated_at),
            'membership_end_requested_at' => $time($member->membership_end_requested_at),
            'application' => $decision ? [
                'applied_on' => $decision->applied_at?->toDateString(),
                'agreed_to_code_of_conduct_and_privacy_policy_at' => $time($decision->agreed_at),
            ] : null,
        ];

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="ern-my-data-'.now()->toDateString().'.json"',
            'Cache-Control' => 'no-store, private',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
