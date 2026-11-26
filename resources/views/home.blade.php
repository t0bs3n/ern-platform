@extends('layouts.site', ['nav' => 'home', 'title' => '', 'description' => 'Esports Research Network is an international network of researchers advancing interdisciplinary esports studies.'])

@section('content')
    <section class="hero-section">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>Research-focused. <span class="text-primary">Globally connected.</span></h1>
                <p class="x-herolead">Esports Research Network is an international network of researchers advancing interdisciplinary esports studies. We connect scholars and industry to explore esports’ global, digital, and innovative impact.</p>
                <div class="hero-buttons">
                    <a href="{{ url('/join') }}" class="btn btn-primary btn-lg">Join ERN</a>
                    <a href="{{ url('/conferences') }}" class="btn btn-outline btn-lg">{{ $event['name'] }} Details</a>
                </div>
            </div>
        </div>
    </section>

    <section class="x-routes" aria-labelledby="routes-h">
        <div class="container-ern">
            <h2 class="x-vh" id="routes-h">Where to start</h2>
            <div class="x-three">
                @foreach ([
                    ['search', 'Find research', 'Publications, conference abstracts and recorded talks in one search.', [['Search the database', '/database'], ['ERNC26 abstracts and talks', '/conferences/ernc26/book'], ['Reading lists', '/database/lists']]],
                    ['cap', 'Teach with ERN', 'Lectures prepared for the classroom, free to use.', [['Teaching units for one class', '/teaching#sec-units'], ['The autumn lecture series', '/teaching#sec-autumn'], ['Run it as a course', '/teaching/guide']]],
                    ['users', 'Join and contribute', 'Become a member, present your work, help the network.', [['Become a member', '/join'], ['Propose a lecture', '/teaching/propose'], ['Ways to get involved', '/get-involved']]],
                ] as [$icon, $heading, $text, $links])
                    <div class="x-card x-route">
                        <div class="action-icon"><x-icon :name="$icon" /></div>
                        <h3>{{ $heading }}</h3>
                        <p>{{ $text }}</p>
                        <ul>
                            @foreach ($links as [$label, $href])
                                <li><a href="{{ url($href) }}">{{ $label }} <x-icon name="arrow" class="x-ic-sm" /></a></li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-spacing bg-background">
        <div class="container-ern">
            <div class="conference-card">
                <div style="display:flex;flex-direction:column;gap:2rem">
                    <div style="flex:1">
                        <div class="conference-badge"><span></span> Featured Event</div>
                        <h2>{{ $event['name'] }}: {{ $event['theme'] }}</h2>
                        <div class="conference-info">
                            <div class="conference-info-item"><x-icon name="cal" /><span>{{ $event['dates'] }}</span></div>
                            <div class="conference-info-item"><x-icon name="clock" /><span>{{ $event['mode'] }}</span></div>
                            <div class="conference-info-item"><x-icon name="globe" /><span>{{ $event['place'] }}</span></div>
                        </div>
                        <div class="conference-tags" style="margin-bottom:0">
                            <a href="{{ url('/conferences/'.strtolower($event['name']).'/call') }}" class="badge-circle">Call for Abstracts</a>
                            <a href="{{ url('/conferences/'.strtolower($event['name']).'/submit') }}" class="badge-circle">Submit</a>
                            <a href="{{ url('/code-of-conduct') }}" class="badge-circle">Code of Conduct</a>
                        </div>
                    </div>
                    <div style="flex-shrink:0"><a href="{{ url('/conferences') }}" class="btn btn-primary btn-lg">Learn More</a></div>
                </div>
            </div>
        </div>
    </section>

    @if ($comingUp->isNotEmpty())
        <section class="x-sponsorband" aria-labelledby="coming-up-h">
            <div class="container-ern">
                <div class="x-row x-between" style="margin-bottom:.75rem">
                    <h2 id="coming-up-h" style="font-size:1.125rem;margin:0">Coming up</h2>
                    <a class="x-small" href="{{ url('/events') }}">All events and deadlines</a>
                </div>
                <ul class="x-upnext">
                    @foreach ($comingUp as $date)
                        <li><a href="{{ url('/conferences/'.strtolower($event['name'])) }}"><span class="x-item-meta">{{ $date['date']->format('l j F Y') }}</span><strong>{{ $event['name'] }}: {{ $date['label'] }}</strong></a></li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($news->isNotEmpty())
        <section class="section-spacing bg-background" aria-labelledby="news-h">
            <div class="container-ern">
                <div class="section-header-flex">
                    <div>
                        <h2 id="news-h">News &amp; Updates</h2>
                        <p style="color:hsl(var(--muted-foreground));margin:0">Stay up to date with the latest announcements and updates from the ERN community</p>
                    </div>
                    <a href="{{ url('/news') }}" class="btn btn-outline">View all <x-icon name="arrow" class="x-ic-sm" /></a>
                </div>
                <div class="news-grid">
                    @foreach ($news as $post)
                        <a class="news-card focus-ring" href="{{ url('/news/'.$post->slug) }}">
                            <div class="news-date"><x-icon name="cal" />{{ $post->published_at->format('j M Y') }}</div>
                            <h3>{{ $post->title }}</h3>
                            <p>{{ $post->summary }}</p>
                            <span class="x-newsfoot">Read the post <x-icon name="arrow" /></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section-spacing bg-background" aria-labelledby="goals-h">
        <div class="container-ern">
            <div class="goals-grid">
                <div>
                    <h2 id="goals-h">What ERN Does</h2>
                    <p style="color:hsl(var(--muted-foreground));margin-bottom:2rem;max-width:42rem">Our mission is to advance esports research through collaboration, education, and connection.</p>
                    @include('partials.goals')
                </div>
                <div class="goals-image"><div class="x-photo"><img src="{{ asset('img/group.jpg') }}" alt="The participants of ERNC26 in Tsukuba, September 2026" loading="lazy" width="1280" height="853"></div></div>
            </div>
        </div>
    </section>

    <section class="section-spacing bg-secondary-light" aria-labelledby="involved-h">
        <div class="container-ern">
            <div class="section-header">
                <h2 id="involved-h">Get Involved</h2>
                <p>Multiple ways to participate in the ERN community</p>
            </div>
            @include('partials.involved-cards')
        </div>
    </section>
@endsection
