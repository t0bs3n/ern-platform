<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Page;
use Illuminate\View\View;

/** The Content tab: news and pages (R1-01). Routes carry can:edit-content. */
class ContentController extends Controller
{
    public function index(): View
    {
        return view('admin.content.index', [
            'news' => News::orderByDesc('published_at')->orderByDesc('id')->get(),
            'pages' => Page::orderBy('title')->get(),
        ]);
    }
}
