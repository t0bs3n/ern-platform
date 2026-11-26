<?php

namespace App\Http\Controllers;

use App\Calendar\EventCalendar;
use App\Models\News;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The member overview (R1-14): the member's own status, ERN notices, the next events and the roles.
 * Saved items, new articles and the press come with later releases.
 */
class MemberAreaController extends Controller
{
    public function index(Request $request, EventCalendar $calendar): View
    {
        return view('members.index', [
            'me' => $request->user(),
            'notices' => Notice::published()->limit(6)->get(),
            'events' => $calendar->upcoming()->take(3)->values(),
            'news' => News::published()->orderByDesc('published_at')->orderByDesc('id')->limit(3)->get(),
        ]);
    }
}
