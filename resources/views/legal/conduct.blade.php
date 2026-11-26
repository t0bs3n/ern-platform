@extends('layouts.site', ['title' => $page->title, 'description' => 'The Code of Conduct of the Esports Research Network and its conferences.'])

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>{{ $page->title }}</h1>
            </div>
        </div>
    </section>
    @if (count($sections) > 1)
        <x-subnav :items="$sections" />
    @endif
    <div class="container-ern content-wrap">
        <div class="x-cols x-side-first">
            <article class="entry-content x-prose">{{ $html }}</article>
            <aside class="x-card">
                <h2 class="x-card-h">Something happened?</h2>
                <p class="x-small x-muted">You can report a concern at any time, also anonymously. The ERN Ombudsperson reads every report.</p>
                <a href="{{ url('/report-a-concern') }}" class="btn btn-primary x-btn-sm">Report a concern</a>
            </aside>
        </div>
    </div>
@endsection
