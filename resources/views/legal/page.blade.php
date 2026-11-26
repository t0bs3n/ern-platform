@extends('layouts.site', ['title' => $page->title])

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>{{ $page->title }}</h1>
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap">
        <article class="entry-content x-prose" style="max-width:48rem">{{ $html }}</article>
    </div>
@endsection
