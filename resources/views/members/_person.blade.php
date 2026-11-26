<div class="x-card x-person">
    <span class="x-av" aria-hidden="true">{{ $person->initials() }}</span>
    <div>
        <a class="x-pname" href="{{ route('members.show', $person->slug) }}">{{ $person->full_name }}</a>
        <span class="x-small x-muted">{{ collect([$person->career_stage, $person->country])->filter()->implode(' · ') }}</span>
        @if ($person->interestTags())
            <div class="x-item-tags">
                @foreach ($person->interestTags() as $tag)
                    <span class="x-tag x-grey">{{ $tag }}</span>
                @endforeach
            </div>
        @endif
        @if ($person->open_to_collaboration)
            <p class="x-small" style="margin:.625rem 0 0">Open to: Collaboration</p>
        @endif
        @if ($person->directoryDiscord())
            <p class="x-small x-dc" style="margin:.375rem 0 0"><span class="x-muted">Discord</span> {{ $person->directoryDiscord() }}</p>
        @endif
    </div>
</div>
