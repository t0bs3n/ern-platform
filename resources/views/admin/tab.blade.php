@extends('admin.layout', ['pageTitle' => $tab === 'overview' ? 'Admin area' : $label.' | Admin area'])

@section('panel')
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
@endsection
