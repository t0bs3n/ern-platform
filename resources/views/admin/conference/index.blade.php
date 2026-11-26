@extends('admin.layout', ['tab' => 'conference', 'pageTitle' => 'Conference | Admin area'])

@section('panel')
    @can('manage-conference')
        <div class="x-card">
            <h2 class="x-card-h">Conferences</h2>
            <ul class="x-admlist">
                @foreach ($conferences as $conference)
                    <li>
                        <span>{{ $conference->name }}: {{ $conference->theme }}
                            <small class="x-muted">{{ $conference->datesText() }} · {{ $conference->status === \App\Models\Conference::UPCOMING ? 'Upcoming' : 'Past' }}</small>
                        </span>
                        <a class="x-link" href="{{ route('admin.conference.edit', $conference) }}">Edit<span class="x-vh"> {{ $conference->name }}</span></a>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div>
            <h2 class="x-h2">Conference</h2>
            <p class="x-muted">The submissions for review come here with release 5.</p>
        </div>
    @endcan
@endsection
