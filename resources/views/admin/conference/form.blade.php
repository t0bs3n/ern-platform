@extends('admin.layout', ['tab' => 'conference', 'pageTitle' => 'Edit '.$conference->name.' | Admin area'])

@section('panel')
    <form class="x-card x-stack" style="gap:1rem;max-width:48rem" method="POST" novalidate action="{{ route('admin.conference.update', $conference) }}">
        @csrf
        @method('PUT')
        <div class="x-row x-between">
            <h2 class="x-card-h" style="margin:0">Edit {{ $conference->name }}</h2>
            <a class="x-link" href="{{ route('admin.conference.index') }}">Back to the conferences</a>
        </div>

        <x-field name="theme" label="Theme" :value="old('theme', $conference->theme)" required />
        <x-field name="place" label="Place" :value="old('place', $conference->place)" required />
        <x-field name="mode" label="Format" :value="old('mode', $conference->mode)" help="For example: In person, Online, Hybrid" required />
        <x-field name="host" label="Host" :value="old('host', $conference->host)" />
        <div class="x-two">
            <x-field name="start_date" label="First day" type="date" :value="old('start_date', $conference->start_date?->format('Y-m-d'))" required />
            <x-field name="end_date" label="Last day" type="date" :value="old('end_date', $conference->end_date?->format('Y-m-d'))" required />
        </div>
        <fieldset class="x-fieldset">
            <legend>Status</legend>
            @foreach ([\App\Models\Conference::UPCOMING => 'Upcoming: shown as the next conference', \App\Models\Conference::PAST => 'Past: shown in the archive'] as $value => $text)
                <label class="x-check" for="f-status-{{ $value }}"><input type="radio" id="f-status-{{ $value }}" name="status" value="{{ $value }}" @checked(old('status', $conference->status) === $value)><span>{{ $text }}</span></label>
            @endforeach
        </fieldset>

        <fieldset class="x-fieldset">
            <legend>Key dates <small>Shown on the conference page, the start page and under "Coming up". Leave a row empty to drop it.</small></legend>
            @php $rows = old('key_dates', collect($conference->key_dates ?? [])->values()->all()); @endphp
            @for ($i = 0; $i < max(6, count($rows) + 2); $i++)
                <div class="x-two">
                    <x-field :name="'key_dates.'.$i.'.label'" :id="'f-kd-'.$i.'-label'" label="Date {{ $i + 1 }}: what happens" :value="$rows[$i]['label'] ?? ''" />
                    <x-field :name="'key_dates.'.$i.'.date'" :id="'f-kd-'.$i.'-date'" label="Date {{ $i + 1 }}: day" type="date" :value="$rows[$i]['date'] ?? ''" />
                </div>
            @endfor
        </fieldset>

        <div class="x-row"><button class="btn btn-primary" type="submit">Save</button></div>
    </form>
@endsection
