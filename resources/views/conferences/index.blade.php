@extends('layouts.site', ['nav' => 'conferences', 'title' => 'Conferences', 'description' => 'The Esports Research Network Conference (ERNC): the next conference and the archive of past ones.'])

@section('content')
    @if ($featured)
        <section class="hero-section x-hero-conf">
            <div class="container-ern">
                <div style="max-width:64rem">
                    <h1 style="margin-bottom:1.5rem">{{ $featured->title ?? $featured->name }}</h1>
                    <div style="margin-bottom:1.5rem">@include('conferences._info', ['conference' => $featured])</div>
                    <div class="conference-tags" style="margin-bottom:1rem">
                        <a href="{{ route('conferences.show', [$featured, 'call']) }}" class="badge-circle">Call for Abstracts</a>
                        <a href="{{ route('conferences.submit', $featured) }}" class="badge-circle">Submit an Abstract</a>
                    </div>
                    @if ($featured->keyDates()->isNotEmpty())
                        <ul class="ern-key-dates">
                            @foreach ($featured->keyDates() as $date)
                                <li class="conference-info-item"><x-icon name="cal" /><span><strong>{{ $date['label'] }}:</strong> {{ $date['date']->format('l j F Y') }}</span></li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="hero-buttons" style="margin-top:2rem"><a href="#sec-archive" class="btn btn-primary btn-lg">Explore Past Conferences</a></div>
                </div>
            </div>
        </section>
        <section class="section-spacing bg-background">
            <div class="container-ern">
                <div class="about-section-with-image">
                    <div>
                        @if ($featured->slug === 'ernc27')
                            <img src="{{ asset('img/ernc27.png') }}" alt="ERNC27" style="height:2.5rem;width:auto;margin-bottom:1.25rem">
                        @endif
                        <h2>{{ $featured->theme }}</h2>
                        <div class="x-intro" style="color:hsl(var(--muted-foreground));line-height:1.75">{{ $featured->introHtml() }}</div>
                        <a href="{{ route('conferences.show', [$featured, 'call']) }}" class="btn btn-outline">Read the Call for Abstracts</a>
                    </div>
                    <div><img src="{{ asset('img/hall.jpg') }}" alt="A session in the Main Hall at ERNC26" loading="lazy" style="width:100%;height:auto;border-radius:.75rem"></div>
                </div>
            </div>
        </section>
    @else
        <section class="hero-section x-short x-hero-conf">
            <div class="container-ern"><div style="max-width:64rem"><h1>Conferences</h1></div></div>
        </section>
    @endif

    <section id="sec-archive" class="section-spacing bg-secondary-light">
        <div class="container-ern">
            <div class="section-header">
                <h2>Past ERN Conferences</h2>
                <p>Explore research and recordings from past ERN conferences</p>
            </div>
            <div class="quick-actions-grid">
                @foreach ($past as $conference)
                    <div class="action-card">
                        <div class="action-icon"><x-icon name="cal" /></div>
                        <h3>{{ $conference->name }}</h3>
                        <p style="margin-bottom:.5rem">{{ $conference->theme }}</p>
                        <p class="ern-conf-meta">{{ $conference->place }} · {{ $conference->datesText() }}</p>
                        <div style="display:flex;flex-wrap:wrap;gap:.5rem">
                            <a href="{{ route('conferences.show', $conference) }}" class="badge-circle">Summary<span class="x-vh"> of {{ $conference->name }}</span></a>
                            @if ($conference->book_pdf_url)
                                <a href="{{ $conference->book_pdf_url }}" target="_blank" rel="noopener noreferrer" class="badge-circle">Abstracts (PDF)<span class="x-vh"> of {{ $conference->name }}, opens in a new tab</span></a>
                            @endif
                            @if ($conference->videos_url)
                                <a href="{{ $conference->videos_url }}" target="_blank" rel="noopener noreferrer" class="badge-circle">Videos<span class="x-vh"> of {{ $conference->name }} (opens in a new tab)</span></a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
