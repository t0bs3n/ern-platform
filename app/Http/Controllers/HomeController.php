<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $event = config('ern.featured_event');

        // The next three key dates that have not passed.
        $comingUp = collect($event['key_dates'] ?? [])
            ->map(fn (array $date) => ['label' => $date[0], 'date' => Carbon::parse($date[1])])
            ->filter(fn (array $date) => $date['date']->endOfDay()->isFuture())
            ->sortBy('date')
            ->take(3)
            ->values();

        return view('home', [
            'news' => News::published()->orderByDesc('published_at')->orderByDesc('id')->limit(3)->get(),
            'event' => $event,
            'comingUp' => $comingUp,
        ]);
    }
}
