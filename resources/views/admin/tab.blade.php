@extends('layouts.site', ['nav' => 'members', 'title' => $tab === 'overview' ? 'Admin area' : $label.' | Admin area'])

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
            @foreach ($tabs as $key => [$tabLabel])
                <a class="x-tab" href="{{ $key === 'overview' ? route('admin') : route('admin.tab', $key) }}"{!! $key === $tab ? ' aria-current="page"' : '' !!}>{{ $tabLabel }}</a>
            @endforeach
        </nav>

        <div class="x-stack" style="gap:2rem">
            @if ($tab === 'system')
                <div class="x-card">
                    <h2 class="x-card-h">System</h2>
                    <dl class="x-facts x-small">
                        <div><dt>Version</dt><dd>{{ $version }}</dd></div>
                        <div><dt>Environment</dt><dd>{{ $environment }}</dd></div>
                        <div><dt>Last scheduled run</dt><dd>
                            @if ($lastScheduledRun)
                                {{ $lastScheduledRun->setTimezone('UTC')->format('j F Y, H:i') }} UTC
                            @else
                                Not yet. The scheduler has not run.
                            @endif
                        </dd></div>
                    </dl>
                </div>
            @else
                <div>
                    <h2 class="x-h2">{{ $label }}</h2>
                    <p class="x-muted">This part of the admin area is being built.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
