@php
    $editing = $event->exists;
    $zone = old('timezone', $event->timezone ?? config('ern.event_timezone'));
    $local = fn ($moment) => $moment?->setTimezone($event->timezone)->format('H:i');
@endphp
@extends('admin.layout', ['tab' => 'content', 'pageTitle' => ($editing ? 'Edit event' : 'New event').' | Admin area'])

@section('panel')
    <div class="x-stack" style="max-width:40rem">
        <form class="x-card x-stack" style="gap:1rem" method="POST" novalidate
              action="{{ $editing ? route('admin.content.events.update', $event) : route('admin.content.events.store') }}">
            @csrf
            @if ($editing) @method('PUT') @endif
            <div class="x-row x-between">
                <h2 class="x-card-h" style="margin:0">{{ $editing ? 'Edit event' : 'New event' }}</h2>
                <a class="x-link" href="{{ route('admin.tab', 'content') }}">Back to the content</a>
            </div>
            <p class="x-small x-muted" style="margin:0">Conference days and their key dates appear by themselves. Add everything else here.</p>

            <x-field name="title" label="Title" :value="old('title', $event->title)" required />
            <fieldset class="x-fieldset">
                <legend>Kind</legend>
                @foreach (\App\Models\Event::KINDS as $value)
                    <label class="x-check" for="f-kind-{{ $value }}"><input type="radio" id="f-kind-{{ $value }}" name="kind" value="{{ $value }}" @checked(old('kind', $event->kind) === $value)><span>{{ $value }}</span></label>
                @endforeach
                @error('kind') <span class="x-err">{{ $message }}</span> @enderror
            </fieldset>
            <x-field name="date" label="Date" type="date" :value="old('date', $event->date?->format('Y-m-d'))" required />
            <div class="x-two">
                <x-field name="start_time" label="Start time" type="time" :value="old('start_time', $local($event->starts_at))" help="Leave empty for a deadline or an all-day date." />
                <x-field name="end_time" label="End time" type="time" :value="old('end_time', $local($event->ends_at))" />
            </div>
            <label class="x-field" for="f-timezone">
                <span>Time zone</span>
                <select class="x-input" id="f-timezone" name="timezone" @error('timezone') aria-invalid="true" aria-describedby="f-timezone-err" @enderror>
                    @foreach (DateTimeZone::listIdentifiers() as $identifier)
                        <option value="{{ $identifier }}" @selected($zone === $identifier)>{{ str_replace('_', ' ', $identifier) }}</option>
                    @endforeach
                </select>
                <small>The time is shown in this zone, with its name.</small>
                @error('timezone') <span class="x-err" id="f-timezone-err">{{ $message }}</span> @enderror
            </label>
            <x-field name="place" label="Place" :value="old('place', $event->place)" help="For example: On Zoom, or NC State University, Raleigh, USA" />
            <x-field name="link" label="Link" :value="old('link', $event->link)" help="Optional: https://… or an address on this site, such as /members" />

            <div class="x-row"><button class="btn btn-primary" type="submit">Save</button></div>
        </form>

        @if ($editing)
            <form class="x-card" method="POST" action="{{ route('admin.content.events.destroy', $event) }}">
                @csrf
                @method('DELETE')
                <h2 class="x-card-h">Remove the event</h2>
                <p class="x-small x-muted">It disappears from the list and from everyone's calendar.</p>
                <button class="btn btn-outline" type="submit">Remove {{ $event->title }}</button>
            </form>
        @endif
    </div>
@endsection
