@extends('layouts.site', ['nav' => 'members', 'title' => 'Welcome to ERN'])

@section('content')
    <x-member-gate>
        <form class="x-card x-login" method="POST" action="{{ $action }}" novalidate>
            @csrf
            <h2 class="x-card-h">Welcome to ERN</h2>
            <p class="x-small x-muted">The board has confirmed your membership. Choose a password to open the member area.</p>
            <x-field name="password" label="New password" type="password" autocomplete="new-password" help="At least eight characters." />
            <x-field name="password_confirmation" id="f-password2" label="Repeat the password" type="password" autocomplete="new-password" />
            <div class="x-row x-between">
                <button class="btn btn-primary" type="submit">Set password and sign in</button>
                <a class="x-link" href="{{ route('login') }}">I already have a password</a>
            </div>
        </form>
    </x-member-gate>
@endsection
