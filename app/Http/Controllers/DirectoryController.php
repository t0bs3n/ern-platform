<?php

namespace App\Http\Controllers;

use App\Mail\MemberMessage;
use App\Models\User;
use App\Support\Like;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

/**
 * The member directory and researcher pages (R1-13), for approved members only.
 * Only listed members appear, only with their released fields, and never with an e-mail address.
 * A hidden member answers exactly like an unknown address.
 */
class DirectoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => Str::limit(trim((string) $request->query('q')), 100, ''),
            'topic' => Str::limit(trim((string) $request->query('topic')), 100, ''),
            'country' => Str::limit(trim((string) $request->query('country')), 100, ''),
            'stage' => in_array($request->query('stage'), User::CAREER_STAGES, true) ? $request->query('stage') : '',
            'open' => $request->query('open') === 'collaboration' ? 'collaboration' : '',
        ];

        $query = User::inDirectory()->select(User::DIRECTORY_COLUMNS);

        foreach (preg_split('/\s+/', $filters['q'], -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $like = Like::contains($word);
            $query->where(function (Builder $q) use ($like) {
                foreach (['first_name', 'last_name', 'country', 'career_stage', 'interests'] as $column) {
                    $q->orWhereRaw($column." LIKE ? ESCAPE '!'", [$like]);
                }
            });
        }
        if ($filters['topic'] !== '') {
            $query->whereRaw("interests LIKE ? ESCAPE '!'", [Like::contains($filters['topic'])]);
        }
        if ($filters['country'] !== '') {
            $query->where('country', $filters['country']);
        }
        if ($filters['stage'] !== '') {
            $query->where('career_stage', $filters['stage']);
        }
        if ($filters['open'] !== '') {
            $query->where('open_to_collaboration', true);
        }

        // Filter choices come only from listed members, so a hidden member leaves no trace.
        $listed = User::inDirectory()->get(['country', 'interests']);

        return view('members.directory', [
            'members' => $query->orderBy('last_name')->orderBy('first_name')->paginate(24)->withQueryString(),
            'total' => User::inDirectory()->count(),
            'filters' => $filters,
            'filtered' => array_filter($filters) !== [],
            'countries' => $listed->pluck('country')->filter()->unique()->sort()->values(),
            'topics' => $listed->flatMap->interestTags()->unique(fn ($t) => Str::lower($t))->sort(SORT_NATURAL | SORT_FLAG_CASE)->values(),
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        return view('members.show', ['person' => $this->findListed($slug), 'me' => $request->user()]);
    }

    /** Forwards a message by e-mail. The message is stored nowhere. */
    public function contact(Request $request, string $slug): RedirectResponse|Response
    {
        $person = $this->findListed($slug);
        Gate::authorize('contact-member', $person);

        $request->validate(
            ['contact_message' => ['required', 'string', 'max:5000']],
            ['contact_message.required' => 'Write a few words first.', 'contact_message.string' => 'Write a few words first.', 'contact_message.max' => 'This is too long.'],
        );

        $recipient = User::whereKey($person->id)->first();

        try {
            Mail::to($recipient->email)->send(new MemberMessage($request->user(), $recipient, (string) $request->input('contact_message')));
        } catch (Throwable $e) {
            Log::warning('A message between members could not be sent.', ['sender' => $request->user()->id, 'error' => $e::class]);

            return response()->view('members.show', [
                'person' => $person,
                'me' => $request->user(),
                'failed' => true,
                'draft' => (string) $request->input('contact_message'),
            ], 503);
        }

        return redirect()->route('members.show', $person->slug)
            ->with('status', 'Your message is sent. '.$person->first_name.' gets it by e-mail and can answer you directly.');
    }

    private function findListed(string $slug): User
    {
        return User::inDirectory()->select(User::DIRECTORY_COLUMNS)->where('slug', $slug)->firstOr(fn () => abort(404));
    }
}
