<?php

namespace App\Http\Controllers;

use App\Calendar\EventCalendar;
use App\Calendar\IcsWriter;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/** Events and deadlines (R1-08): the list and the calendar feed. Public content only. */
class EventController extends Controller
{
    public function index(Request $request, EventCalendar $calendar): View
    {
        $kind = in_array($request->query('kind'), Event::KINDS, true) ? $request->query('kind') : null;

        return view('events.index', ['entries' => $calendar->upcoming($kind), 'kind' => $kind]);
    }

    public function feed(EventCalendar $calendar, IcsWriter $writer): Response
    {
        $ics = $writer->write($calendar->upcoming(), 'ERN: Events and deadlines', parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'esportsresearch.net');

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="ern-events.ics"',
        ]);
    }
}
