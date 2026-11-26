{{-- The admin area frame: header and tab bar. Pages set $tab (the current tab key) and a 'panel' section. --}}
@extends('layouts.site', ['nav' => 'members', 'title' => $pageTitle ?? 'Admin area'])

@section('content')
    <div class="container-ern content-wrap x-stack">
        <div class="x-row x-between">
            <div>
                <h1 style="font-size:1.75rem">Admin area</h1>
                <span class="x-small x-muted">Signed in as {{ auth()->user()->full_name }}</span>
            </div>
            <div class="x-row">
                <span class="x-tag x-grey"><x-icon name="lock" />Board and admins only</span>
                <a href="{{ route('members') }}" class="btn btn-outline x-btn-sm">Back to the member area</a>
            </div>
        </div>

        <nav class="x-tabs x-many" aria-label="Admin area">
            @foreach (\App\Http\Controllers\Admin\AdminAreaController::visibleTabs() as $key => [$tabLabel])
                <a class="x-tab" href="{{ $key === 'overview' ? route('admin') : route('admin.tab', $key) }}"{!! $key === $tab ? ' aria-current="page"' : '' !!}>{{ $tabLabel }}</a>
            @endforeach
        </nav>

        @if (session('status'))
            <div class="x-card" role="status"><p class="x-small" style="margin:0">{{ session('status') }}</p></div>
        @endif

        <div class="x-stack" style="gap:2rem">
            @yield('panel')
        </div>
    </div>
@endsection
