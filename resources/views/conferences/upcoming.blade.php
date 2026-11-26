@extends('layouts.site', ['nav' => 'conferences', 'title' => $conference->name.': Call for Abstracts', 'description' => $conference->name.': '.$conference->theme])

@section('content')
    <section class="hero-section x-short x-hero-conf">
        <div class="container-ern">
            <div style="max-width:64rem">
                <ol class="x-crumbs"><li><a href="{{ route('conferences.index') }}">Conferences</a></li><li aria-current="page">{{ $conference->name }}</li></ol>
                @php [$titlePart, $leadPart] = array_pad(explode(': ', $conference->theme, 2), 2, null); @endphp
                <h1>{{ $conference->name }}: {{ $titlePart }}</h1>
                @if ($leadPart)
                    <p>{{ $leadPart }}</p>
                @endif
                @include('conferences._info', ['withMode' => false])
            </div>
        </div>
    </section>
    <div class="container-ern x-tabwrap">
        <nav class="x-tabs x-many" aria-label="{{ $conference->name }}">
            <a class="x-tab" href="{{ route('conferences.show', [$conference, 'call']) }}" aria-current="page">Call for Abstracts</a>
            <a class="x-tab" href="{{ route('conferences.submit', $conference) }}">Submit</a>
        </nav>
    </div>
    @if (count($sections) > 1)
        <x-subnav :items="$sections" />
    @endif
    <div class="container-ern content-wrap x-stack">
        <div class="x-cols x-side-first">
            <article class="entry-content x-prose">
                @if (count($sections) > 0)
                    {{ $html }}
                @else
                    <p>The Call for Abstracts is being prepared.</p>
                @endif
            </article>
            <aside class="x-stack" style="gap:1rem">
                <div class="x-card">
                    <h2 class="x-card-h">At a glance</h2>
                    <dl class="x-facts x-small" style="margin-bottom:1rem">
                        @foreach ($conference->keyDates() as $date)
                            <div style="grid-template-columns:8.5rem minmax(0,1fr)"><dt>{{ $date['label'] }}</dt><dd>{{ $date['date']->format('j F Y') }}</dd></div>
                        @endforeach
                        <div style="grid-template-columns:8.5rem minmax(0,1fr)"><dt>Conference</dt><dd>{{ $conference->datesText() }}</dd></div>
                    </dl>
                    <a href="{{ route('conferences.submit', $conference) }}" class="btn btn-primary x-btn-sm">Submit an abstract</a>
                </div>
            </aside>
        </div>
    </div>
@endsection
