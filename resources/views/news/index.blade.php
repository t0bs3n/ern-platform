@extends('layouts.site', ['nav' => 'news', 'title' => 'News', 'description' => 'Announcements and updates from the Esports Research Network.'])

@push('head')
    <link rel="alternate" type="application/rss+xml" title="ERN News" href="{{ route('feed.news') }}">
@endpush

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>News</h1>
                <p>Stay up to date with the latest announcements and updates from the ERN community</p>
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap x-stack" style="gap:2rem">
        @if ($news->isEmpty())
            <p class="x-muted">No news yet.</p>
        @else
            <div class="news-grid">
                @foreach ($news as $post)
                    <a class="news-card focus-ring" href="{{ route('news.show', $post->slug) }}">
                        <div class="news-date"><x-icon name="cal" />{{ $post->published_at->format('j M Y') }}</div>
                        <h2 class="x-news-h">{{ $post->title }}</h2>
                        <p>{{ $post->summary }}</p>
                        <span class="x-newsfoot">Read the post <x-icon name="arrow" /></span>
                    </a>
                @endforeach
            </div>
            @if ($news->hasPages())
                <nav class="news-pagination" aria-label="News pages">
                    @if (! $news->onFirstPage())
                        <a class="page-numbers" href="{{ $news->previousPageUrl() }}" rel="prev">Newer posts</a>
                    @endif
                    @foreach (range(1, $news->lastPage()) as $number)
                        @if ($number === $news->currentPage())
                            <span class="page-numbers current" aria-current="page">{{ $number }}</span>
                        @else
                            <a class="page-numbers" href="{{ $news->url($number) }}">{{ $number }}</a>
                        @endif
                    @endforeach
                    @if ($news->hasMorePages())
                        <a class="page-numbers" href="{{ $news->nextPageUrl() }}" rel="next">Older posts</a>
                    @endif
                </nav>
            @endif
        @endif

        @auth
            <div class="x-card x-row x-between">
                <div><h2 class="x-card-h" style="margin-bottom:.25rem">More in your member area</h2><p class="x-small x-muted" style="margin:0">New journal articles and the esports press, picked up every day.</p></div>
            </div>
        @else
            <div class="x-card x-row x-between">
                <div><h2 class="x-card-h" style="margin-bottom:.25rem;display:flex;flex-wrap:wrap;align-items:center;gap:.5rem">More for members <span class="x-tag x-grey"><x-icon name="lock" />Members only</span></h2><p class="x-small x-muted" style="margin:0">Members also get new journal articles and the esports press, picked up every day.</p></div>
                <div class="x-row"><a href="{{ url('/join') }}" class="btn btn-primary x-btn-sm">Become a member</a><a href="{{ route('login') }}" class="btn btn-outline x-btn-sm">Sign in</a></div>
            </div>
        @endauth
        <p class="x-small x-muted" style="margin:0"><a href="{{ route('feed.news') }}">Subscribe to the news feed (RSS)</a></p>
    </div>
@endsection
