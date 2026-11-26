@extends('layouts.site', ['nav' => 'involved', 'title' => 'Get Involved', 'description' => 'Ways to take part in the Esports Research Network.'])

@php $grey = 'color:hsl(var(--muted-foreground));line-height:1.75'; @endphp

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>Get Involved</h1>
                <p>This network is built by its community. Whether you are a researcher, student, practitioner, or simply curious about esports.</p>
            </div>
        </div>
    </section>
    <x-subnav :items="[['join', 'Join'], ['community', 'Community'], ['ways', 'Ways to Participate'], ['start', 'How to Get Started']]" />

    <section id="sec-join" class="section-spacing bg-background">
        <div class="container-ern">
            <div class="section-header">
                <h2>Get Involved</h2>
                <p>Multiple ways to participate in the ERN community</p>
            </div>
            @include('partials.involved-cards')
        </div>
    </section>

    <section id="sec-community" class="section-spacing bg-secondary-light">
        <div class="container-ern">
            <div class="about-section-with-image">
                <div>
                    <h2>A Community-Led Network</h2>
                    <div style="{{ $grey }}">{{ $community?->bodyHtml() }}</div>
                </div>
                <div><img src="{{ asset('img/group.jpg') }}" alt="The participants of ERNC26 in Tsukuba, September 2026" loading="lazy" style="width:100%;height:auto;border-radius:.75rem"></div>
            </div>
        </div>
    </section>

    <section id="sec-ways" class="section-spacing bg-background">
        <div class="container-ern">
            <div class="section-header">
                <h2>Ways to Participate</h2>
                <p>Participation can be formal or informal, short-term or ongoing. Every contribution matters.</p>
            </div>
            <div class="quick-actions-grid">
                <div class="action-card"><div class="action-icon"><x-icon name="coffee" /></div><h3>Community</h3><p>Help host informal coffee chats, welcome new members, and support discussions on Discord. You can contribute by creating a friendly, inclusive, and active community space.</p></div>
                <div class="action-card"><div class="action-icon"><x-icon name="book" /></div><h3>Research</h3><p>Support ongoing research projects, working groups, or data collection activities. You can contribute skills, ideas, or time at different levels of involvement.</p></div>
                <div class="action-card"><div class="action-icon"><x-icon name="cap" /></div><h3>Education</h3><p>Assist with lectures, seminars, or learning activities across the network. You can help with organization, moderation, or content preparation.</p></div>
                <div class="action-card"><div class="action-icon"><x-icon name="cal" /></div><h3>Events</h3><p>Volunteer in planning, organizing, or supporting online, hybrid, or local events. This includes moderation, facilitation, and logistical support.</p></div>
                <div class="action-card"><div class="action-icon"><x-icon name="mega" /></div><h3>Outreach</h3><p>Help connect the ERN with new audiences, institutions, and communities. You can support communication, partnerships, and visibility activities.</p></div>
                <div class="action-card"><div class="action-icon"><x-icon name="bulb" /></div><h3>Knowledge</h3><p>Contribute to blogs, summaries, resources, or shared materials. You can help make research and discussions accessible to wider audiences.</p></div>
                <div class="action-card"><div class="action-icon"><x-icon name="hand" /></div><h3>Students</h3><p>Support student-led initiatives or participate as a student volunteer. Opportunities include research assistance, event support, and community activities.</p></div>
                <div class="action-card"><div class="action-icon"><x-icon name="globe" /></div><h3>Practice</h3><p>Share applied perspectives from industry or professional contexts. You can help bridge research and practice through collaboration and dialogue.</p></div>
                <div class="action-card"><div class="action-icon"><x-icon name="doc" /></div><h3>Policy</h3><p>Support engagement with policy and governance discussions related to esports. You can help prepare briefs, discussions, or events connecting research and policy.</p></div>
                <div class="action-card"><div class="action-icon"><x-icon name="award" /></div><h3>Impact</h3><p>Help translate network activities into meaningful outcomes. You can support evaluation, storytelling, and initiatives that demonstrate social value.</p></div>
            </div>
        </div>
    </section>

    <section id="sec-start" class="section-spacing bg-secondary-light">
        <div class="container-ern">
            <div style="max-width:48rem">
                <h2>How to Get Started</h2>
                <div style="{{ $grey }}">{{ $start?->bodyHtml() }}</div>
                <div class="hero-buttons" style="animation:none;margin-top:1.5rem">
                    <a href="{{ url('/join') }}" class="btn btn-primary btn-lg">Become a Member</a>
                    <a href="https://discord.gg/N9TbYD74e8" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-lg">Join Discord <x-icon name="ext" class="x-ic-sm" /><span class="x-vh"> (opens in a new tab)</span></a>
                </div>
                <p class="x-small x-muted" style="margin:1.25rem 0 0">Contact the board: board@esportsresearch.net</p>
            </div>
        </div>
    </section>
@endsection
