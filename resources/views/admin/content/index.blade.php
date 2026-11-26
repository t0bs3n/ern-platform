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
                            <span>{{ $page->title }} <small class="x-muted">{{ $page->slug }}</small></span>
                            <a class="x-link" href="{{ route('admin.content.pages.edit', $page) }}">Edit<span class="x-vh"> {{ $page->title }}</span></a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    
        <div class="x-card">
            <div class="x-row x-between" style="margin-bottom:.75rem">
                <h2 class="x-card-h" style="margin:0">Board and volunteers</h2>
                <a class="btn btn-outline x-btn-sm" href="{{ route('admin.content.people.create') }}">Add a person</a>
            </div>
            @foreach (\App\Models\TeamMember::GROUPS as $group => $groupLabel)
                <h3 class="x-h4">{{ $groupLabel }}</h3>
                <ul class="x-admlist">
                    @forelse (\App\Models\TeamMember::inGroup($group)->get() as $person)
                        <li>
                            <span>{{ $person->name }} <small class="x-muted">{{ $person->role_title }}</small></span>
                            <a class="x-link" href="{{ route('admin.content.people.edit', $person) }}">Edit<span class="x-vh"> {{ $person->name }}</span></a>
                        </li>
                    @empty
                        <li><span class="x-muted">Nobody yet.</span></li>
                    @endforelse
                </ul>
            @endforeach
        </div>
    
        <div class="x-card">
            <div class="x-row x-between" style="margin-bottom:.75rem">
                <h2 class="x-card-h" style="margin:0">Events and deadlines</h2>
                <a class="btn btn-outline x-btn-sm" href="{{ route('admin.content.events.create') }}">New event</a>
            </div>
            <p class="x-small x-muted">Conference days and key dates appear by themselves. Past events stay here.</p>
            <ul class="x-admlist">
                @forelse (\App\Models\Event::orderByDesc('date')->get() as $item)
                    <li>
                        <span>{{ $item->title }} <small class="x-muted">{{ $item->date->format('j M Y') }} · {{ $item->kind }}</small></span>
                        <a class="x-link" href="{{ route('admin.content.events.edit', $item) }}">Edit<span class="x-vh"> {{ $item->title }}</span></a>
                    </li>
                @empty
                    <li><span class="x-muted">No events yet.</span></li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
