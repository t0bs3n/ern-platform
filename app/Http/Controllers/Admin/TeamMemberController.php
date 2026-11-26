<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TeamMemberRequest;
use App\Models\TeamMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/** Editors change the board and the volunteers on About Us (R1-04). Routes carry can:edit-content. */
class TeamMemberController extends Controller
{
    public function create(): View
    {
        return view('admin.content.team-form', ['person' => new TeamMember(['group' => 'board'])]);
    }

    public function store(TeamMemberRequest $request): RedirectResponse
    {
        $person = new TeamMember($request->validated());
        $person->position ??= (int) TeamMember::where('group', $person->group)->max('position') + 10;
        $person->save();

        return redirect()->route('admin.tab', 'content')->with('status', 'Saved: '.$person->name);
    }

    public function edit(TeamMember $person): View
    {
        return view('admin.content.team-form', ['person' => $person]);
    }

    public function update(TeamMemberRequest $request, TeamMember $person): RedirectResponse
    {
        $old = $person->position;
        $person->fill($request->validated());
        $person->position ??= $old;
        $person->save();

        return redirect()->route('admin.tab', 'content')->with('status', 'Saved: '.$person->name);
    }

    public function destroy(TeamMember $person): RedirectResponse
    {
        abort_unless(Gate::allows('edit-content'), 403);

        $person->delete();

        return redirect()->route('admin.tab', 'content')->with('status', 'Removed from About Us: '.$person->name);
    }
}
