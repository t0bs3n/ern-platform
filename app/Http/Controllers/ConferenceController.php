<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Page;
use App\Support\Markdown;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Conferences (R1-06): the overview, each past conference's summary, and the ERNC27 call. */
class ConferenceController extends Controller
{
    public function index(): View
    {
        return view('conferences.index', [
            'featured' => Conference::featured(),
            'past' => Conference::past()->get(),
        ]);
    }

    public function show(Conference $conference, ?string $tab = null): View
    {
        if ($conference->status === Conference::UPCOMING) {
            // Only the call exists for now; programme, registration and sponsors come in release 4.
            abort_unless(in_array($tab, [null, 'call'], true), 404);
            $call = Page::where('slug', $conference->slug.'-call-for-abstracts')->first();

            return view('conferences.upcoming', ['conference' => $conference] + Markdown::withSections($call?->body));
        }

        // The Book of Abstracts as pages comes in release 4.
        abort_unless(in_array($tab, [null, 'summary'], true), 404);

        return view('conferences.past', ['conference' => $conference]);
    }

    /** "Submit an abstract": the submission form on the current website, until release 5. */
    public function submit(Conference $conference): RedirectResponse
    {
        abort_unless($conference->status === Conference::UPCOMING, 404);

        return redirect()->away((string) config('ern.submission_url'));
    }
}
