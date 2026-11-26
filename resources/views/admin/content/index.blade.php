@extends('admin.layout', ['tab' => 'content', 'pageTitle' => 'Content | Admin area'])

@section('panel')
    <div class="x-two" style="gap:1.5rem;align-items:start">
        <div class="x-card">
            <div class="x-row x-between" style="margin-bottom:.75rem">
                <h2 class="x-card-h" style="margin:0">News</h2>
                <a class="btn btn-outline x-btn-sm" href="{{ route('admin.content.news.create') }}">New post</a>
            </div>
            @if ($news->isEmpty())
                <p class="x-small x-muted">No posts yet.</p>
            @else
                <ul class="x-admlist">
                    @foreach ($news as $post)
                        <li>
                            <span>{{ $post->title }}
                                <small class="x-muted">
                                    @if ($post->status !== \App\Models\News::PUBLISHED)
                                        Draft
                                    @elseif ($post->isPublic())
                                        {{ $post->published_at->format('j M Y') }}
                                    @else
                                        Scheduled, {{ $post->published_at?->format('j M Y') }}
                                    @endif
                                </small>
                            </span>
                            <a class="x-link" href="{{ route('admin.content.news.edit', $post) }}">Edit<span class="x-vh"> {{ $post->title }}</span></a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="x-card">
            <div class="x-row x-between" style="margin-bottom:.75rem">
                <h2 class="x-card-h" style="margin:0">Pages</h2>
                <a class="btn btn-outline x-btn-sm" href="{{ route('admin.content.pages.create') }}">New page</a>
            </div>
            @if ($pages->isEmpty())
                <p class="x-small x-muted">No pages yet.</p>
            @else
                <ul class="x-admlist">
                    @foreach ($pages as $page)
                        <li>
                            <span>{{ $page->title }} <small class="x-muted">/{{ $page->slug }}</small></span>
                            <a class="x-link" href="{{ route('admin.content.pages.edit', $page) }}">Edit<span class="x-vh"> {{ $page->title }}</span></a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
