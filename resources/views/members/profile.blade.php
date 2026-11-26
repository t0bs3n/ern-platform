@extends('layouts.site', ['nav' => 'members', 'title' => 'My details'])

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <ol class="x-crumbs"><li><a href="{{ route('members') }}">Member area</a></li><li aria-current="page">My details</li></ol>
                <h1>My details</h1>
                <p>Keep your entry in the member list up to date yourself. No new application is needed.</p>
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap x-stack" style="max-width:48rem">
        @if (session('status'))
            <div class="x-card" role="status"><p class="x-small" style="margin:0">{{ session('status') }}</p></div>
        @endif

        <form class="x-card x-profile" method="POST" action="{{ route('members.profile.update') }}" novalidate>
            @csrf
            @method('PUT')
            <h2 class="x-card-h">My details</h2>
            <div class="x-two">
                <x-field name="first_name" label="First name" :value="old('first_name', $member->first_name)" autocomplete="given-name" />
                <x-field name="last_name" label="Last name" :value="old('last_name', $member->last_name)" autocomplete="family-name" />
            </div>
            <x-field name="institution" label="University or organisation" :value="old('institution', $member->institution)" autocomplete="organization" />
            <x-field name="country" label="Country" :value="old('country', $member->country)" autocomplete="country-name" />
            <x-field name="city" label="City" :value="old('city', $member->city)" autocomplete="address-level2" help="Only you and the admins see your city." />
            <x-select name="career_stage" label="Status" :options="\App\Models\User::CAREER_STAGES" :value="old('career_stage', $member->career_stage)" />
            <x-textarea name="interests" label="Research interests" rows="3" :value="old('interests', $member->interests)" />
            <x-field name="orcid" label="ORCID iD" :value="old('orcid', $member->orcid)" placeholder="0000-0000-0000-0000" />
            <x-field name="discord_name" label="Discord name" :value="old('discord_name', $member->discord_name)" />

            <h2 class="x-card-h" style="margin-top:1.5rem">Visibility</h2>
            <p class="x-small x-muted">Signed-in members see your name, career stage, institution, country, research interests and ORCID iD. Never your e-mail address.</p>
            @foreach ([
                'listed_in_directory' => 'Show me in the member directory',
                'open_to_collaboration' => 'I am open to collaboration requests',
                'show_discord_in_directory' => 'Show my Discord name in the member directory',
            ] as $field => $label)
                <label class="x-check" for="f-{{ $field }}"><input type="checkbox" id="f-{{ $field }}" name="{{ $field }}" value="1" @checked(old($field, $member->{$field}))><span>{{ $label }}</span></label>
            @endforeach
            <button class="btn btn-primary" type="submit" style="margin-top:.75rem">Save changes</button>
        </form>

        <form class="x-card x-profile" method="POST" action="{{ route('members.profile.email') }}" novalidate>
            @csrf
            <h2 class="x-card-h">E-mail address</h2>
            <p class="x-small">Your address: <strong>{{ $member->email }}</strong></p>
            @if ($member->pending_email)
                <p class="x-small x-muted">Waiting for confirmation since {{ $member->pending_email_requested_at?->format('j F Y, H:i') }} UTC: {{ $member->pending_email }}</p>
            @endif
            <x-field name="new_email" label="New e-mail address" type="email" :value="old('new_email')" autocomplete="email" />
            <x-field name="current_password" label="Current password" type="password" autocomplete="current-password" />
            <button class="btn btn-outline" type="submit">Change e-mail address</button>
        </form>

        <div class="x-card">
            <h2 class="x-card-h">Download my data</h2>
            <p class="x-small">A file with everything the platform stores about you.</p>
            <a class="btn btn-outline x-btn-sm" href="{{ route('members.profile.data') }}">Download my data</a>
        </div>

        <form class="x-card" method="POST" action="{{ route('members.profile.end') }}" novalidate>
            @csrf
            <h2 class="x-card-h">End my membership</h2>
            <p class="x-small">Your request goes to the board. Nothing is deleted until the board has spoken with you.</p>
            <label class="x-check" for="f-confirm"><input type="checkbox" id="f-confirm" name="confirm" value="1" @error('confirm') aria-invalid="true" aria-describedby="f-confirm-err" @enderror><span>I want to end my ERN membership</span></label>
            @error('confirm') <span class="x-err" id="f-confirm-err">{{ $message }}</span> @enderror
            <div style="margin-top:.75rem"><button class="btn btn-outline" type="submit">Send the request</button></div>
        </form>
    </div>
@endsection
