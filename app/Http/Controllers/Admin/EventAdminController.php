<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/** Editors add events and deadlines (R1-08). Routes carry can:edit-content. */
class EventAdminController extends Controller
{
    public function create(): View
    {
        return view('admin.content.event-form', ['event' => new Event(['kind' => 'Association', 'timezone' => config('ern.event_timezone')])]);
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $event = Event::create($request->eventValues());

        return redirect()->route('admin.tab', 'content')->with('status', 'Saved: '.$event->title);
    }

    public function edit(Event $event): View
    {
        return view('admin.content.event-form', ['event' => $event]);
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $event->update($request->eventValues());

        return redirect()->route('admin.tab', 'content')->with('status', 'Saved: '.$event->title);
    }

    public function destroy(Event $event): RedirectResponse
    {
        abort_unless(Gate::allows('edit-content'), 403);
        $event->delete();

        return redirect()->route('admin.tab', 'content')->with('status', 'Removed: '.$event->title);
    }
}
