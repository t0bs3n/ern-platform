<?php

namespace App\Http\Controllers;

use App\Calendar\EventCalendar;
use App\Models\Conference;
use App\Models\News;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(EventCalendar $calendar): View
    {
        return view('home', [
            'news' => News::published()->orderByDesc('published_at')->orderByDesc('id')->limit(3)->get(),
            'event' => Conference::featured(),
            // The next three events and deadlines of any kind (R1-08).
            'comingUp' => $calendar->upcoming()->take(3)->values(),
        ]);
    }
}
