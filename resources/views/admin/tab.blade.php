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
    @elseif ($tab === 'overview')
        <div>
            <h2 class="x-h2">Needs attention</h2>
            @if (empty($attention))
                <p class="x-muted">Nothing waits for you at the moment.</p>
            @else
                <ul class="x-list">
                    @foreach ($attention as $item)
                        <li class="x-item x-slim">
                            <div class="x-item-row x-admrow">
                                <div class="x-item-main"><span class="x-item-title" style="font-size:.9375rem">{{ $item['text'] }}</span><span class="x-item-by">Owner: {{ $item['owner'] }}</span></div>
                                <a class="btn btn-outline x-btn-sm" href="{{ $item['url'] }}">{{ $item['action'] }}</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        @if (! empty($tiles))
            <div class="x-things x-four">
                @foreach ($tiles as $tile)
                    <a class="x-card x-thing" href="{{ $tile['url'] }}"><b>{{ $tile['count'] }}</b><span>{{ $tile['label'] }}</span></a>
                @endforeach
            </div>
        @endif
        <div class="x-two" style="gap:1.5rem;align-items:start">
            <div class="x-card">
                <h2 class="x-card-h">Who sees what</h2>
                <dl class="x-facts x-small">
                    @foreach ([
                        ['Board', 'Everything in this area'],
                        ['Conference team', 'Submissions, reviewers, program and sponsors'],
                        ['Reviewers', 'Their assigned abstracts, with comments and a recommendation'],
                        ['Final editor', 'One person for all submissions. Gives the final result and the feedback to the authors'],
                        ['Admin', 'Also sets the rights of the other members'],
                        ['Database Lead', 'The database queue and the reading lists'],
                        ['Lecture team', 'Proposals, the autumn program and the teaching units'],
                        ['Editors', 'News and notices'],
                        ['Ombudsperson', 'Concerns only. They never appear in this area.'],
                    ] as [$who, $what])
                        <div style="grid-template-columns:9rem minmax(0,1fr)"><dt>{{ $who }}</dt><dd>{{ $what }}</dd></div>
                    @endforeach
                </dl>
            </div>
            @if ($comingUp->isNotEmpty())
                <div class="x-card">
                    <h2 class="x-card-h">Coming up</h2>
                    <ul class="x-admlist">
                        @foreach ($comingUp as $entry)
                            <li><span>{{ $entry->title }} <small class="x-muted">{{ $entry->whenText() }}</small></span></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @else
        <div>
            <h2 class="x-h2">{{ $label }}</h2>
            <p class="x-muted">This part of the admin area is being built.</p>
        </div>
    @endif
@endsection
