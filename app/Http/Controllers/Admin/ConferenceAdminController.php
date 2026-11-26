<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConferenceRequest;
use App\Models\Conference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/** The Conference tab (R1-06): the conference team changes dates and details. */
class ConferenceAdminController extends Controller
{
    public function index(): View
    {
        abort_unless(Gate::any(AdminAreaController::TABS['conference'][1]), 403);

        return view('admin.conference.index', [
            'conferences' => Gate::allows('manage-conference') ? Conference::orderByDesc('start_date')->get() : collect(),
        ]);
    }

    public function edit(Conference $conference): View
    {
        return view('admin.conference.form', ['conference' => $conference]);
    }

    public function update(ConferenceRequest $request, Conference $conference): RedirectResponse
    {
        $conference->fill($request->safe()->except('key_dates'));
        $conference->key_dates = $request->keyDates();
        $conference->save();

        return redirect()->route('admin.conference.index')->with('status', 'Saved: '.$conference->name);
    }
}
