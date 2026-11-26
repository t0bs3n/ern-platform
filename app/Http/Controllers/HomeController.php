<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\News;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $event = Conference::featured();

        // The next three key dates of the featured conference that have not passed.
        // R1-08 adds the other events and deadlines.
        $comingUp = $event === null ? collect() : $event->keyDates()
            ->filter(fn (array $date) => $date['date']->copy()->endOfDay()->isFuture())
            ->take(3)
            ->values();

        return view('home', [
            'news' => News::published()->orderByDesc('published_at')->orderByDesc('id')->limit(3)->get(),
            'event' => $event,
            'comingUp' => $comingUp,
        ]);
    }
}
