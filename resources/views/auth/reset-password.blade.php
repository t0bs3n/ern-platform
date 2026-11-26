@extends('layouts.site', ['nav' => 'members', 'title' => 'Set a new password'])

@section('content')
    <x-member-gate>
        <form class="x-card x-login" method="POST" action="{{ route('password.update') }}" novalidate>
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <h2 class="x-card-h">Set a new password</h2>
            @error('email')
                <p class="x-err" role="alert">{{ $message }} <a href="{{ route('password.request') }}">Ask for a new link</a>.</p>
            @enderror
            <x-field name="email" label="E-mail address" type="email" :value="old('email', $email)" autocomplete="username" required />
            <x-field name="password" label="New password" type="password" autocomplete="new-password" help="At least eight characters." required />
            <x-field name="password_confirmation" id="f-password2" label="Repeat the password" type="password" autocomplete="new-password" required />
            <button class="btn btn-primary" type="submit">Save the new password</button>
        </form>
    </x-member-gate>
@endsection
