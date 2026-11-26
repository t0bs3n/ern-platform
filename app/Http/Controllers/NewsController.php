<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Response;
use Illuminate\View\View;

/** The public news (R1-03): list, single post and the RSS feed. Only published posts. */
class NewsController extends Controller
{
    public function index(): View
    {
        $news = News::published()->orderByDesc('published_at')->orderByDesc('id')->paginate(12);

        abort_if($news->isEmpty() && $news->currentPage() > 1, 404);

        return view('news.index', ['news' => $news]);
    }

    public function show(string $slug): View
    {
        $news = News::published()->where('slug', $slug)->firstOrFail();

        return view('news.show', ['news' => $news]);
    }

    public function feed(): Response
    {
        $news = News::published()->orderByDesc('published_at')->orderByDesc('id')->limit(10)->get();

        return response()
            ->view('news.feed', ['news' => $news], 200)
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
