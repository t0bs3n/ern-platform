<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

/** Podcast and ERN Live (R1-07). Nothing from YouTube or Twitch loads before a visitor clicks. */
class MediaController extends Controller
{
    public function podcast(): View
    {
        return view('media.podcast', [
            'intro' => Page::where('slug', 'podcast-intro')->first(),
            'podcast' => config('ern.podcast'),
        ]);
    }

    public function live(): View
    {
        return view('media.live', ['live' => config('ern.live')]);
    }
}
