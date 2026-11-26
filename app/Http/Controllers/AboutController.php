<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\TeamMember;
use Illuminate\View\View;

/** About Us and Get Involved (R1-04). Texts and people are content that editors change. */
class AboutController extends Controller
{
    public function about(): View
    {
        return view('about.about', [
            'mission' => $this->block('about-mission'),
            'history' => $this->block('about-history'),
            'former' => $this->block('about-former-board'),
            'board' => TeamMember::inGroup('board')->get(),
            'governance' => TeamMember::inGroup('governance')->get(),
            'figures' => config('ern.member_figures'),
        ]);
    }

    public function involved(): View
    {
        return view('about.involved', [
            'community' => $this->block('get-involved-community'),
            'start' => $this->block('get-involved-start'),
        ]);
    }

    private function block(string $slug): ?Page
    {
        return Page::where('slug', $slug)->first();
    }
}
