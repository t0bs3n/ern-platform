@extends('layouts.site', ['nav' => 'members', 'title' => 'Member directory'])

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <ol class="x-crumbs"><li><a href="{{ route('members') }}">Member area</a></li><li aria-current="page">Directory</li></ol>
                <h1>Member directory</h1>
                <p>Find members by country and research topic.</p>
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap x-stack">
        <form method="GET" action="{{ route('members.directory') }}" class="x-stack" style="gap:1rem" role="search">
            <div class="x-search">
                <x-icon name="search" />
                <label class="x-vh" for="mem-q">Search the member directory</label>
                <input id="mem-q" name="q" type="search" value="{{ $filters['q'] }}" placeholder="Search by name, country or research topic" maxlength="100" autocomplete="off">
                <button class="btn btn-primary x-btn-sm" type="submit">Search</button>
            </div>
            <div class="x-filters">
                @foreach ([
                    ['topic', 'Topic', $topics->mapWithKeys(fn ($t) => [$t => $t])->all()],
                    ['country', 'Country', $countries->mapWithKeys(fn ($c) => [$c => $c])->all()],
                    ['stage', 'Career stage', array_combine(\App\Models\User::CAREER_STAGES, \App\Models\User::CAREER_STAGES)],
                    ['open', 'Open to', ['collaboration' => 'Collaboration']],
                ] as [$name, $label, $options])
                    <label class="x-select" for="dir-{{ $name }}">
                        <span>{{ $label }}</span>
                        <select id="dir-{{ $name }}" name="{{ $name }}">
                            <option value="">All</option>
                            @foreach ($options as $value => $text)
                                <option value="{{ $value }}" @selected($filters[$name] === (string) $value)>{{ $text }}</option>
                            @endforeach
                        </select>
                    </label>
                @endforeach
                <button class="btn btn-outline x-btn-sm" type="submit">Search</button>
            </div>
            <p class="x-small x-muted" style="margin:0">Open a member to write through ERN by e-mail. Discord is optional.</p>
        </form>

        <p class="x-filterline" aria-live="polite">
            <span>{{ $filtered ? $members->total().' of '.$total.' members' : $total.' members' }}</span>
            @if ($filtered)
                <a class="x-link" href="{{ route('members.directory') }}">Clear search and filters</a>
            @endif
        </p>

        @if ($members->isEmpty())
            <div class="x-empty">No member found. Try a country or a topic, for example Japan or governance.</div>
        @else
            <div class="x-people">
                @foreach ($members as $person)
                    @include('members._person')
                @endforeach
            </div>
            @if ($members->hasPages())
                <nav class="news-pagination" aria-label="Directory pages">
                    @if (! $members->onFirstPage())<a class="page-numbers" href="{{ $members->previousPageUrl() }}" rel="prev">Previous</a>@endif
                    <span class="page-numbers current" aria-current="page">{{ $members->currentPage() }}</span>
                    @if ($members->hasMorePages())<a class="page-numbers" href="{{ $members->nextPageUrl() }}" rel="next">Next</a>@endif
                </nav>
            @endif
        @endif
    </div>
@endsection
