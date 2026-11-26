<div class="conference-info">
    <div class="conference-info-item"><x-icon name="cal" /><span>{{ $conference->datesText() }}</span></div>
    @if ($withMode ?? true)
        <div class="conference-info-item"><x-icon name="clock" /><span>{{ $conference->mode }}</span></div>
    @endif
    <div class="conference-info-item"><x-icon name="globe" /><span>{{ $conference->place }}</span></div>
</div>
