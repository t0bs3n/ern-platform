@extends('layouts.site', ['nav' => 'members', 'title' => 'Set a new password'])

@section('content')
    <x-member-gate>
        @if (session('reset_link_requested'))
            <div class="x-card" role="status">
                <div class="x-success">
                    <x-icon name="check" />
                    <div>
                        <h2 class="x-card-h">Check your e-mail</h2>
                        <p class="x-small">If this address belongs to a member, a link to set a new password is on its way.</p>
                        <p class="x-small x-muted" style="margin:0">Nothing after ten minutes? Look in the spam folder, then write to board@esportsresearch.net.</p>
                    </div>
                </div>
                <p style="margin:.75rem 0 0"><a class="x-link" href="{{ route('login') }}">Back to sign in</a></p>
            </div>
        @else
            <form class="x-card x-login" method="POST" action="{{ route('password.email') }}" novalidate>
                @csrf
                <h2 class="x-card-h">Set a new password</h2>
                <p class="x-small x-muted">Enter the e-mail address of your membership. We send you a link.</p>
                <x-field name="email" label="E-mail address" type="email" :value="old('email')" autocomplete="username" placeholder="you@university.edu" required />
                <div class="x-row">
                    <button class="btn btn-primary" type="submit">Send the link</button>
                    <a class="x-link" href="{{ route('login') }}">Back to sign in</a>
                </div>
            </form>
        @endif
    </x-member-gate>
@endsection
