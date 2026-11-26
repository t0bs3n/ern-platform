@extends('layouts.site', ['nav' => 'live', 'title' => 'ERN Live', 'description' => 'The Twitch channel of the Esports Research Network.'])

@php $channelUrl = 'https://www.twitch.tv/'.$live['channel']; @endphp

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>ERN Live</h1>
                <p>Our Twitch channel on the site.</p>
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap x-stack">
        <div class="x-cols x-wide-side">
            <div>
                {{-- Nothing loads from Twitch until the visitor asks for it (docs/DECISIONS.md: no third parties on page load). --}}
                <div class="x-video" id="live-player" data-channel="{{ $live['channel'] }}" style="margin-bottom:1rem">
                    <img src="{{ asset('img/hall.jpg') }}" alt="">
                    <div class="x-video-over">
                        <strong style="font-size:1.25rem">Watch ERN Live</strong>
                        <span class="x-video-cap">
                            @if (filled($live['next_stream']))
                                Next stream: {{ $live['next_stream'] }}.
                            @endif
                            The player comes from Twitch. It loads only when you start it, and Twitch then sets its own cookies.
                        </span>
                        <div class="x-row">
                            <button type="button" class="btn btn-primary x-btn-sm" data-live-start hidden>Start the player here</button>
                            <a href="{{ $channelUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline x-btn-sm">Open the channel on Twitch<span class="x-vh"> (opens in a new tab)</span></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="x-stack" style="gap:1rem">
                <div class="x-card">
                    <h2 class="x-card-h">Recordings</h2>
                    <p class="x-small x-muted">After a stream, each talk is cut and placed next to its abstract.</p>
                    <div style="display:grid;gap:.75rem">
                        @foreach ($live['recordings'] as [$label, $title])
                            <a class="x-small" style="font-weight:500" href="{{ $live['recordings_url'] }}" target="_blank" rel="noopener noreferrer"><span class="x-muted" style="display:block;font-weight:400">{{ $label }}</span>{{ $title }}<span class="x-vh"> (opens in a new tab)</span></a>
                        @endforeach
                    </div>
                    <p style="margin-top:1rem"><a href="{{ url('/conferences/ernc26') }}" class="x-small">All ERNC26 talks with their abstracts</a></p>
                </div>
            </div>
        </div>
        <div class="x-card">
            <h2 class="x-card-h">Good to know</h2>
            <dl class="x-facts x-small">
                <div><dt>Watching</dt><dd>Works on this page without any account.</dd></div>
                <div><dt>Chat</dt><dd>Writing needs a free Twitch account. Reading does not.</dd></div>
                <div><dt>Afterwards</dt><dd>The recording appears on the page of the lecture or the talk.</dd></div>
            </dl>
        </div>
    </div>
@endsection
