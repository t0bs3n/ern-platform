@extends('layouts.site', ['nav' => 'members', 'title' => 'Sign in'])

@section('content')
    <x-member-gate>
        <form class="x-card x-login" method="POST" action="{{ route('login.store') }}" novalidate>
            @csrf
            <h2 class="x-card-h">Sign in</h2>
            <x-field name="email" label="E-mail address" type="email" :value="old('email')" autocomplete="username" placeholder="you@university.edu" required />
            <x-field name="password" label="Password" type="password" autocomplete="current-password" required />
            <div class="x-row x-between">
                <button class="btn btn-primary" type="submit">Sign in</button>
                <a class="x-link" href="{{ route('password.request') }}">Forgot your password?</a>
            </div>
            <p class="x-small x-muted" style="margin:1rem 0 0">New member? Your welcome e-mail holds a link to set your password first.</p>
        </form>
    </x-member-gate>
@endsection
