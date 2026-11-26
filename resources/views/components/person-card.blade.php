@props(['person'])
<div class="x-member">
    <span class="x-av" aria-hidden="true">{{ $person->initials() }}</span>
    <div><strong>{{ $person->name }}</strong>@if ($person->affiliation)<span>{{ $person->affiliation }}</span>@endif<em>{{ $person->role_title }}</em></div>
</div>
