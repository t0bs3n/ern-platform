@extends('layouts.site', ['nav' => 'members', 'title' => 'Member area'])

@section('content')
    <div class="container-ern content-wrap x-stack">
        <div class="x-row x-between">
            <div class="x-row">
                <span class="x-av x-org" aria-hidden="true">{{ $me->initials() }}</span>
                <div>
                    <h1 style="font-size:1.75rem">Welcome back, {{ $me->first_name }}</h1>
                    <span class="x-small x-muted">{{ collect([$me->career_stage, $me->institution])->filter()->implode(' · ') }}</span>
                </div>
            </div>
            <div class="x-row">
                @can('open-admin-area')
                    <a class="btn btn-primary x-btn-sm" href="{{ route('admin') }}">Admin area</a>
                @endcan
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline x-btn-sm" type="submit">Sign out</button>
                </form>
            </div>
        </div>

        <nav class="x-tabs x-many" aria-label="Member area">
            <a class="x-tab" href="{{ route('members') }}" aria-current="page">Overview</a>
            <a class="x-tab" href="{{ route('members.directory') }}">Directory</a>
            <a class="x-tab" href="{{ route('members.profile') }}">My profile</a>
        </nav>

        @if (session('status'))
            <div class="x-card" role="status"><p class="x-small" style="margin:0">{{ session('status') }}</p></div>
        @endif

        <div class="x-cols x-wide-side">
            <div class="x-stack" style="gap:2rem">
                @if ($notices->isNotEmpty())
                    <div id="notices">
                        <h2 class="x-h2">Notices</h2>
                        <ul class="x-list">
                            @foreach ($notices as $notice)
                                <li class="x-item">
                                    <div class="x-item-main">
                                        <span class="x-item-meta">{{ $notice->kind }}</span>
                                        <span class="x-item-title">{{ $notice->title }}</span>
                                        <div class="x-item-by entry-content x-small">{{ $notice->bodyHtml() }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($events->isNotEmpty())
                    <div>
                        <div class="x-row x-between" style="margin-bottom:1rem"><h2 class="x-h2" style="margin:0">Coming up</h2><a class="x-small" href="{{ route('events.index') }}">All events and deadlines</a></div>
                        <ul class="x-list">
                            @foreach ($events as $entry)
                                <li class="x-item"><div class="x-item-row"><a class="x-item-main" href="{{ $entry->link ?? route('events.index') }}"><span class="x-item-meta">{{ $entry->whenText() }}</span><span class="x-item-title">{{ $entry->title }}</span>@if ($entry->place)<span class="x-item-by">{{ $entry->place }}</span>@endif</a><span class="x-item-side"><x-icon name="arrow" /></span></div></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($news->isNotEmpty())
                    <div>
                        <div class="x-row x-between" style="margin-bottom:1rem"><h2 class="x-h2" style="margin:0">From the ERN</h2><a class="x-small" href="{{ route('news.index') }}">All news</a></div>
                        <ul class="x-list">
                            @foreach ($news as $post)
                                <li class="x-item"><div class="x-item-row"><a class="x-item-main" href="{{ route('news.show', $post->slug) }}"><span class="x-item-meta">{{ $post->published_at->format('j M Y') }}</span><span class="x-item-title">{{ $post->title }}</span></a><span class="x-item-side"><x-icon name="arrow" /></span></div></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <aside class="x-stack" style="gap:1rem">
                <div class="x-card">
                    <h2 class="x-card-h">Your membership</h2>
                    <dl class="x-facts x-small" style="margin-bottom:1rem">
                        @foreach ([
                            'Member since' => $me->approved_at?->format('F Y'),
                            'Status' => $me->career_stage ?: 'Not given',
                            'Listed as' => collect([$me->institution, $me->country])->filter()->implode(', ') ?: 'Not given',
                            'Directory' => $me->listed_in_directory ? 'Visible to members' : 'Hidden',
                            'Discord name' => $me->discord_name ?: 'Not given',
                        ] as $label => $value)
                            <div style="grid-template-columns:7rem minmax(0,1fr)"><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>
                        @endforeach
                    </dl>
                    <a class="btn btn-outline x-btn-sm" href="{{ route('members.profile') }}">Update my details</a>
                </div>
                <div class="x-card">
                    <h2 class="x-card-h">Your roles</h2>
                    <ul class="x-admlist">
                        @foreach ($me->roles->sortBy(fn ($r) => array_search($r->role, \App\Enums\Role::cases(), true)) as $held)
                            <li><span>{{ $held->role->label() }} <small class="x-muted">{{ $held->role->description() }}</small></span></li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>
@endsection
