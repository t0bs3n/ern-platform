@extends('layouts.site', ['title' => 'Page not found'])

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>Page not found</h1>
                <p>The address does not match a page on this site.</p>
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap">
        <a href="{{ url('/') }}" class="btn btn-primary">Back to the home page</a>
    </div>
@endsection
