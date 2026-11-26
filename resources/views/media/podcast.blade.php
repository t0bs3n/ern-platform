@extends('layouts.site', ['nav' => 'podcast', 'title' => 'Podcast', 'description' => 'The Esports Research Report: conversations with researchers on gaming and esports.'])

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>{{ $podcast['title'] }}</h1>
                <p>Conversations with researchers on gaming and esports.</p>
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap x-stack" style="gap:2.5rem">
        <div class="x-cols">
            <div class="x-prose" style="font-size:1rem">
                {{ $intro?->bodyHtml() }}
                <p>To take part, write to the host at {{ $podcast['contact'] }} or reach out on the <a href="https://discord.gg/N9TbYD74e8" target="_blank" rel="noopener noreferrer">ERN Discord<span class="x-vh"> (opens in a new tab)</span></a>.</p>
            </div>
            <aside class="x-card">
                <dl class="x-facts x-small" style="border-top:0">
                    @foreach ($podcast['people'] as [$role, $name, $detail])
                        <div style="grid-template-columns:5rem minmax(0,1fr)"><dt>{{ $role }}</dt><dd><strong style="font-weight:600">{{ $name }}</strong><span class="x-muted" style="display:block">{{ $detail }}</span></dd></div>
                    @endforeach
                </dl>
            </aside>
        </div>
        <div>
            <div class="x-row x-between" style="margin-bottom:1.25rem">
                <h2 style="margin:0;font-size:1.5rem">Episodes</h2>
                <a class="x-small" href="{{ $podcast['playlist'] }}" target="_blank" rel="noopener noreferrer">All episodes on YouTube<span class="x-vh"> (opens in a new tab)</span></a>
            </div>
            <div class="x-eps">
                @foreach ($podcast['episodes'] as [$number, $guest, $video])
                    <a class="x-ep focus-ring" href="https://www.youtube.com/watch?v={{ $video }}" target="_blank" rel="noopener noreferrer">
                        <div class="x-ep-art"><b>Episode {{ $number }}</b><span><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M8 5v14l11-7z"></path></svg></span></div>
                        <div class="x-ep-body"><strong>{{ $guest }}</strong><span>Watch on YouTube <x-icon name="ext" class="x-ic-sm" /><span class="x-vh"> (opens in a new tab)</span></span></div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
