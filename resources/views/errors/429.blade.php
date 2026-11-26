@extends('layouts.site', ['title' => 'Too many tries'])

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>Too many tries</h1>
                <p>Too many forms were sent. Please try again later.</p>
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap">
        <a href="{{ url('/') }}" class="btn btn-primary">Back to the home page</a>
    </div>
@endsection
