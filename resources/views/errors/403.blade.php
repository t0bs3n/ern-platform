@extends('layouts.site', ['title' => 'No access'])

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern">
            <div style="max-width:64rem">
                <h1>No access</h1>
                <p>Your account does not have the right to open this page.</p>
            </div>
        </div>
    </section>
    <div class="container-ern content-wrap">
        <a href="{{ url('/') }}" class="btn btn-primary">Back to the home page</a>
    </div>
@endsection
