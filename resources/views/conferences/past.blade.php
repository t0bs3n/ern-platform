@extends('layouts.site', ['nav' => 'conferences', 'title' => $conference->name, 'description' => $conference->name.': '.$conference->theme])

@section('content')
    <section class="hero-section x-short x-hero-conf">
        <div class="container-ern">
            <div style="max-width:64rem">
                <ol class="x-crumbs"><li><a href="{{ route('conferences.index') }}">Conferences</a></li><li aria-current="page">{{ $conference->name }}</li></ol>
                <h1>{{ $conference->name }}: {{ $conference->theme }}</h1>
                @include('conferences._info')
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap x-stack">
        <div class="x-cols x-side-first">
            <article class="entry-content x-prose">
                <h2>Conference Overview</h2>
                {{ $conference->summaryHtml() }}
                @if (! empty($conference->facts))
                    <dl class="x-facts" style="font-size:1rem">
                        @foreach ($conference->facts as [$label, $value])
                            <div><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>
                        @endforeach
                    </dl>
                @endif
            </article>
            <aside class="x-card">
                <h2 class="x-card-h">Abstracts and recordings</h2>
                <div class="x-stack" style="gap:.625rem;align-items:flex-start">
                    @if ($conference->book_pdf_url)
                        <a href="{{ $conference->book_pdf_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary x-btn-sm"><x-icon name="book" />Read the Book of Abstracts<span class="x-vh"> (PDF, opens in a new tab)</span></a>
                    @endif
                    @if ($conference->videos_url)
                        <a href="{{ $conference->videos_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline x-btn-sm"><x-icon name="video" />Videos on YouTube<span class="x-vh"> (opens in a new tab)</span></a>
                    @endif
                </div>
                @if ($conference->old_summary_url)
                    <p class="x-small" style="margin-top:1rem"><a href="{{ $conference->old_summary_url }}" target="_blank" rel="noopener noreferrer">Full summary on the current site<span class="x-vh"> (opens in a new tab)</span></a></p>
                @endif
            </aside>
        </div>
    </div>
@endsection
