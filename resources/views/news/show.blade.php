@extends('layouts.site', ['nav' => 'news', 'title' => $news->title, 'description' => $news->summary])

@section('content')
    <section class="hero-section x-short post-header">
        <div class="container-ern">
            <div style="max-width:64rem">
                <ol class="x-crumbs"><li><a href="{{ route('news.index') }}">News</a></li><li aria-current="page">{{ \Illuminate\Support\Str::limit($news->title, 60) }}</li></ol>
                <h1>{{ $news->title }}</h1>
                <div class="news-date" style="margin:1rem 0 0"><x-icon name="cal" /><time datetime="{{ $news->published_at->toDateString() }}">{{ $news->published_at->format('j F Y') }}</time>@if ($news->category) <span class="news-category-tag">{{ $news->category }}</span>@endif</div>
            </div>
        </div>
    </section>
    <article class="container-ern content-wrap" style="max-width:52rem">
        @if ($news->image_path)
            <img src="{{ route('news.image', $news) }}" alt="{{ $news->image_alt }}" style="border-radius:.75rem;margin-bottom:2rem">
        @endif
        <div class="entry-content">
            @if (filled($news->body))
                {{ $news->bodyHtml() }}
            @else
                <p>{{ $news->summary }}</p>
            @endif
        </div>
        <p style="margin-top:2rem"><a href="{{ route('news.index') }}" class="btn btn-outline">All news</a></p>
    </article>
@endsection
