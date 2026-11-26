@extends('layouts.site', ['nav' => 'members', 'title' => 'Welcome to ERN'])

@section('content')
    <x-member-gate>
        <div class="x-card" role="status">
            @switch($state)
                @case('expired')
                    <h2 class="x-card-h">This link has expired</h2>
                    <p class="x-small">The link in your welcome e-mail works for {{ config('ern.activation_days') }} days. Ask for a new one and we send it to the address of your membership.</p>
                    <form method="POST" action="{{ $resend['action'] }}">
                        @csrf
                        <input type="hidden" name="expires" value="{{ $resend['expires'] }}">
                        <input type="hidden" name="signature" value="{{ $resend['signature'] }}">
                        <button class="btn btn-primary" type="submit">Send a new link</button>
                    </form>
                    @break
                @case('used')
                    <h2 class="x-card-h">This link was already used</h2>
                    <p class="x-small">Your password is set. Sign in with it.</p>
                    <div class="x-row">
                        <a class="btn btn-primary x-btn-sm" href="{{ route('login') }}">Sign in</a>
                        <a class="x-link" href="{{ route('password.request') }}">Forgot your password?</a>
                    </div>
                    @break
                @case('sent')
                    <div class="x-success">
                        <x-icon name="check" />
                        <div>
                            <h2 class="x-card-h">Check your e-mail</h2>
                            <p class="x-small">If your membership still waits for a password, a new link is on its way.</p>
                            <p class="x-small x-muted" style="margin:0">Nothing after ten minutes? Look in the spam folder, then write to board@esportsresearch.net.</p>
                        </div>
                    </div>
                    @break
                @default
                    <h2 class="x-card-h">This link is not valid</h2>
                    <p class="x-small" style="margin:0">Open the link exactly as it is in your welcome e-mail. If that does not work, write to board@esportsresearch.net.</p>
            @endswitch
        </div>
    </x-member-gate>
@endsection
