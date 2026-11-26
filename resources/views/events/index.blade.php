@extends('layouts.site', ['title' => 'Events and deadlines', 'description' => 'Conferences, lectures, deadlines and the dates of the association in one calendar.'])

@push('head')
    <link rel="alternate" type="text/calendar" title="ERN events and deadlines" href="{{ route('events.feed') }}">
@endpush

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>Events and deadlines</h1>
                <p>Conferences, lectures, deadlines and the dates of the association in one calendar.</p>
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap x-stack">
        <nav class="x-chips" aria-label="Kind of event">
            @foreach (['' => 'Everything', 'Conference' => 'Conferences', 'Lecture' => 'Lectures', 'Deadline' => 'Deadlines', 'Association' => 'Association'] as $value => $label)
                <a class="x-chip" href="{{ $value === '' ? route('events.index') : route('events.index', ['kind' => $value]) }}"{!! ($kind ?? '') === $value ? ' aria-current="page"' : '' !!}>{{ $label }}</a>
            @endforeach
        </nav>
        <p class="x-small x-muted" style="margin:0">Times are shown in the time zone of the event, which is named next to the time. Dates without a time are deadlines that end with the day.</p>

        @if ($entries->isEmpty())
            <p class="x-muted">Nothing is planned here at the moment.</p>
        @else
            <ul class="x-list">
                @foreach ($entries as $entry)
                    <li class="x-item">
                        <div class="x-item-row x-admrow">
                            @if ($entry->link)
                                <a class="x-item-main x-plainlink" href="{{ $entry->link }}">
                            @else
                                <div class="x-item-main">
                            @endif
                                <span class="x-item-meta">{{ $entry->whenText() }}</span>
                                <span class="x-item-title">{{ $entry->title }}</span>
                                @if ($entry->place)
                                    <span class="x-item-by">{{ $entry->place }}</span>
                                @endif
                            @if ($entry->link)
                                </a>
                            @else
                                </div>
                            @endif
                            <span class="x-tag x-grey">{{ $entry->kind }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="x-card x-row x-between">
            <div>
                <h2 class="x-card-h" style="margin-bottom:.25rem">In your own calendar</h2>
                <p class="x-small x-muted" style="margin:0">Subscribe once, and new dates appear in your calendar by themselves.</p>
            </div>
            <a class="btn btn-outline x-btn-sm" href="{{ route('events.feed') }}">Subscribe to the calendar (.ics)</a>
        </div>
    </div>
@endsection
