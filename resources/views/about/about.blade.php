@extends('layouts.site', ['nav' => 'about', 'title' => 'About Us', 'description' => 'Connecting researchers, practitioners, and enthusiasts to advance esports research globally.'])

@php $grey = 'color:hsl(var(--muted-foreground));line-height:1.75'; @endphp

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>About the Esports Research Network</h1>
                <p>Connecting researchers, practitioners, and enthusiasts to advance esports research globally.</p>
            </div>
        </div>
    </section>
    <x-subnav :items="[['goals', 'Goals'], ['members', 'Members'], ['mission', 'Mission Statement'], ['board', 'Board Members'], ['governance', 'Governance & Volunteers'], ['history', 'History']]" />

    <section id="sec-goals" class="section-spacing bg-background">
        <div class="container-ern">
            <h2>Our Goals</h2>
            <p style="color:hsl(var(--muted-foreground));margin-bottom:2rem;max-width:42rem">Our mission is to advance esports research through collaboration, education, and connection.</p>
            @include('partials.goals')
        </div>
    </section>

    <section id="sec-members" class="section-spacing bg-secondary-light">
        <div class="container-ern">
            <h2>Members</h2>
            <div class="x-stats">
                <div class="x-stat"><b>{{ $figures['members'] }}</b><span>members</span></div>
                <div class="x-stat"><b>{{ $figures['countries'] }}</b><span>countries</span></div>
                <div class="x-stat"><b>{{ $figures['europe'] }}</b><span>members in {{ $figures['europe_countries'] }} European countries</span></div>
            </div>
            @php $max = max(array_column($figures['largest_groups'], 1)); @endphp
            <div class="x-mapgrid">
                <img src="{{ asset('img/members.svg') }}" alt="World map of the ERN members by country. Most members live in North America, Europe and Australia." width="1000" height="470">
                <div>
                    <h3 style="font-size:1.125rem;margin-bottom:1rem">Largest groups</h3>
                    <ul class="x-bars">
                        @foreach ($figures['largest_groups'] as [$country, $count])
                            <li><span>{{ $country }}</span><i style="width:{{ (int) round($count / $max * 100) }}%"></i><span>{{ $count }}</span></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <p class="x-small x-muted" style="font-style:italic;margin:1.5rem 0 0">As of: {{ $figures['as_of'] }}</p>
        </div>
    </section>

    <section id="sec-mission" class="section-spacing bg-background">
        <div class="container-ern">
            <div class="about-section-with-image">
                <div>
                    <h2>Mission Statement</h2>
                    <div style="{{ $grey }}">{{ $mission?->bodyHtml() }}</div>
                </div>
                <div><img src="{{ asset('img/hall.jpg') }}" alt="A session in the Main Hall at ERNC26" loading="lazy" style="width:100%;height:auto;border-radius:.75rem"></div>
            </div>
        </div>
    </section>

    <section id="sec-board" class="section-spacing bg-secondary-light">
        <div class="container-ern">
            <h2>Board Members</h2>
            <div class="x-team">
                @foreach ($board as $person)
                    <x-person-card :person="$person" />
                @endforeach
            </div>
        </div>
    </section>

    <section id="sec-governance" class="section-spacing bg-background">
        <div class="container-ern">
            <h2>Governance &amp; Volunteers</h2>
            <div class="x-team">
                @foreach ($governance as $person)
                    <x-person-card :person="$person" />
                @endforeach
            </div>
        </div>
    </section>

    <section id="sec-history" class="section-spacing bg-secondary-light">
        <div class="container-ern">
            <div style="max-width:48rem">
                <h2>Our History</h2>
                <div style="{{ $grey }}">{{ $history?->bodyHtml() }}</div>
                @if ($former)
                    <h3 style="font-size:1.25rem;margin:2.5rem 0 .75rem">Former ERN Board Members</h3>
                    <div style="{{ $grey }}">{{ $former->bodyHtml() }}</div>
                @endif
            </div>
        </div>
    </section>
@endsection
