@extends('layouts.site', ['nav' => 'members', 'title' => 'Member area'])

{{-- Placeholder. The member overview is task R1-14. --}}
@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>Welcome back, {{ auth()->user()->first_name }}</h1>
                <p>Your member overview is being built.</p>
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap x-stack">
        @if (session('status'))
            <div class="x-card" role="status"><p class="x-small" style="margin:0">{{ session('status') }}</p></div>
        @endif
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline" type="submit">Sign out</button>
        </form>
    </div>
@endsection
