{{-- The member area for guests: hero, the form on the left, what members find on the right. --}}
<section class="hero-section x-short">
    <div class="container-ern">
        <div style="max-width:64rem">
            <h1>Member area</h1>
            <p>For ERN members only: your own overview, new journal articles, the esports press, the member directory and notices.</p>
        </div>
    </div>
</section>
<div class="container-ern content-wrap">
    <div class="x-cols x-wide-side">
        <div class="x-stack" style="max-width:28rem">
            @if (session('status'))
                <div class="x-card" role="status"><p class="x-small" style="margin:0">{{ session('status') }}</p></div>
            @endif
            {{ $slot }}
            <p class="x-small x-muted" style="margin:0">Not a member yet? <a href="{{ url('/join') }}">Apply online</a>.</p>
        </div>
        <div class="x-card">
            <h2 class="x-card-h">What members find here</h2>
            <div class="goals-list" style="grid-template-columns:1fr">
                @foreach ([
                    ['compass', 'Your overview', 'Your submissions, your saved talks and what is coming up in the ERN.'],
                    ['doc', 'New journal articles', 'What is new in esports research, picked up as it is published.'],
                    ['globe', 'Esports press', 'The headlines of the industry press, collected every day.'],
                    ['users', 'Member directory', 'Find members by country and research topic.'],
                    ['cal', 'Notices', 'General Assembly, elections and documents for members.'],
                    ['pen', 'Your profile', 'Update your own details at any time, with your Discord name.'],
                ] as [$icon, $heading, $text])
                    <div class="goal-item"><div class="goal-item-content"><div class="goal-icon"><x-icon :name="$icon" /></div><div><h3>{{ $heading }}</h3><p>{{ $text }}</p></div></div></div>
                @endforeach
            </div>
        </div>
    </div>
</div>
