@php $editing = $person->exists; @endphp
@extends('admin.layout', ['tab' => 'content', 'pageTitle' => ($editing ? 'Edit person' : 'New person').' | Admin area'])

@section('panel')
    <div class="x-stack" style="max-width:40rem">
        <form class="x-card x-stack" style="gap:1rem" method="POST" novalidate
              action="{{ $editing ? route('admin.content.people.update', $person) : route('admin.content.people.store') }}">
            @csrf
            @if ($editing) @method('PUT') @endif
            <div class="x-row x-between">
                <h2 class="x-card-h" style="margin:0">{{ $editing ? 'Edit person' : 'New person' }}</h2>
                <a class="x-link" href="{{ route('admin.tab', 'content') }}">Back to the content</a>
            </div>
            <p class="x-small x-muted" style="margin:0">Shown on About Us. Only people who hold the role: never candidates of an election.</p>

            <fieldset class="x-fieldset" @error('group') aria-describedby="f-group-err" @enderror>
                <legend>Shown under</legend>
                @foreach (\App\Models\TeamMember::GROUPS as $value => $label)
                    <label class="x-check" for="f-group-{{ $value }}"><input type="radio" id="f-group-{{ $value }}" name="group" value="{{ $value }}" @checked(old('group', $person->group) === $value)><span>{{ $label }}</span></label>
                @endforeach
                @error('group') <span class="x-err" id="f-group-err">{{ $message }}</span> @enderror
            </fieldset>
            <x-field name="name" label="Name" :value="old('name', $person->name)" required />
            <x-field name="affiliation" label="Institution or organization" :value="old('affiliation', $person->affiliation)" help="For example: University of Agder, NO" />
            <x-field name="role_title" label="Role" :value="old('role_title', $person->role_title)" help="For example: Chair, Board Member, Ombudsperson" required />
            <x-field name="position" label="Order" type="number" :value="old('position', $person->position)" help="Smaller numbers come first. Leave empty to add at the end." min="0" />

            <div class="x-row"><button class="btn btn-primary" type="submit">Save</button></div>
        </form>

        @if ($editing)
            <form class="x-card" method="POST" action="{{ route('admin.content.people.destroy', $person) }}">
                @csrf
                @method('DELETE')
                <h2 class="x-card-h">Remove from About Us</h2>
                <p class="x-small x-muted">{{ $person->name }} is no longer shown. This cannot be undone, but the person can be added again.</p>
                <button class="btn btn-outline" type="submit">Remove {{ $person->name }}</button>
            </form>
        @endif
    </div>
@endsection
