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

    <div style="min-width:0">
        <div class="x-row x-between" style="margin-bottom:1rem">
            <h2 class="x-h2" style="margin:0">Member list</h2>
            <form method="POST" action="{{ route('admin.members.export') }}">
                @csrf
                <button class="btn btn-outline x-btn-sm" type="submit">Export the list</button>
            </form>
        </div>
        <form method="GET" action="{{ route('admin.members.index') }}" class="x-search" role="search" style="margin-bottom:1rem">
            <x-icon name="search" />
            <label class="x-vh" for="members-q">Search by name, country or institution</label>
            <input id="members-q" name="q" type="search" value="{{ $q }}" placeholder="Search by name, country or institution" maxlength="100" autocomplete="off">
            <button class="btn btn-primary x-btn-sm" type="submit">Search</button>
        </form>
        <p class="x-filterline">
            <span>{{ $q !== '' ? $members->total().' of '.$memberTotal.' members' : $memberTotal.' members' }}</span>
            @if ($q !== '')<a class="x-link" href="{{ route('admin.members.index') }}">Clear the search</a>@endif
        </p>
        @if ($members->isEmpty())
            <div class="x-empty">No member found.</div>
        @else
            <div class="x-table">
                <table class="x-plaintable">
                    <thead><tr><th>Name</th><th>Status</th><th>Country</th><th>Role on the platform</th><th><span class="x-vh">Actions</span></th></tr></thead>
                    <tbody>
                        @foreach ($members as $member)
                            <tr>
                                <td>{{ $member->full_name }}
                                    @if ($member->membership_end_requested_at)
                                        <span class="x-tag x-grey" style="display:block;width:max-content;margin-top:.25rem">Asked to end the membership on {{ $member->membership_end_requested_at->format('j F Y') }}</span>
                                    @endif
                                </td>
                                <td>{{ $member->career_stage }}</td>
                                <td>{{ $member->country }}</td>
                                <td>{{ $member->roles->sortBy(fn ($r) => array_search($r->role, \App\Enums\Role::cases(), true))->map(fn ($r) => $r->role->label())->implode(', ') }}</td>
                                <td><a class="x-link" href="{{ route('admin.members.show', $member) }}">Edit rights<span class="x-vh"> of {{ $member->full_name }}</span></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($members->hasPages())
                <nav class="news-pagination" aria-label="Member list pages">
                    @if (! $members->onFirstPage())<a class="page-numbers" href="{{ $members->previousPageUrl() }}" rel="prev">Previous</a>@endif
                    <span class="page-numbers current" aria-current="page">{{ $members->currentPage() }}</span>
                    @if ($members->hasMorePages())<a class="page-numbers" href="{{ $members->nextPageUrl() }}" rel="next">Next</a>@endif
                </nav>
            @endif
        @endif
    </div>
@endsection
