@extends('admin.layout', ['tab' => 'members', 'pageTitle' => 'Members | Admin area'])

@section('panel')
    <div>
        <h2 class="x-h2">Applications to confirm</h2>
        @if ($open->isEmpty())
            <p class="x-muted">No applications wait for the board.</p>
        @else
            <ul class="x-list">
                @foreach ($open as $application)
                    <li class="x-item">
                        <div class="x-item-row x-admrow">
                            <div class="x-item-main">
                                <span class="x-item-meta">Applied on {{ $application->created_at->format('j F Y') }}</span>
                                <span class="x-item-title">{{ $application->first_name }} {{ $application->last_name }}</span>
                                <span class="x-item-by">{{ $application->career_stage }} · {{ $application->institution }} · {{ $application->country }}</span>
                            </div>
                            <form method="POST" action="{{ route('admin.members.applications.approve', $application) }}">
                                @csrf
                                <button class="btn btn-primary x-btn-sm" type="submit">Approve<span class="x-vh"> {{ $application->first_name }} {{ $application->last_name }}</span></button>
                            </form>
                            <form method="POST" action="{{ route('admin.members.applications.decline', $application) }}">
                                @csrf
                                <button class="btn btn-outline x-btn-sm" type="submit">Decline<span class="x-vh"> {{ $application->first_name }} {{ $application->last_name }}</span></button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($approved->isNotEmpty() || $declined->isNotEmpty())
        <div>
            <h2 class="x-h2">Decided in the last {{ \App\Models\MembershipApplication::KEEP_DECLINED_DAYS }} days</h2>
            <ul class="x-list">
                @foreach ($approved as $decision)
                    <li class="x-item">
                        <div class="x-item-row x-admrow">
                            <div class="x-item-main">
                                <span class="x-item-meta">Applied on {{ $decision->applied_at->format('j F Y') }}</span>
                                <span class="x-item-title">{{ $decision->member?->full_name ?? 'A member who has since left' }}</span>
                            </div>
                            <span class="x-tag"><x-icon name="check" />{{ $decision->email_sent_at ? 'Approved, welcome e-mail sent' : 'Approved, welcome e-mail not sent' }}</span>
                        </div>
                    </li>
                @endforeach
                @foreach ($declined as $application)
                    <li class="x-item">
                        <div class="x-item-row x-admrow">
                            <div class="x-item-main">
                                <span class="x-item-meta">Applied on {{ $application->created_at->format('j F Y') }}</span>
                                <span class="x-item-title">{{ $application->first_name }} {{ $application->last_name }}</span>
                            </div>
                            <span class="x-tag x-grey">Declined</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
