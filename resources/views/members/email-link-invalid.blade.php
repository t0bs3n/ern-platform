@extends('layouts.site', ['nav' => 'members', 'title' => 'Link not valid'])

@section('content')
    <section class="hero-section x-short">
        <div class="container-ern"><div style="max-width:64rem"><h1>This link is not valid</h1><p>This link is not valid or has expired. Ask for a new one on your profile page.</p></div></div>
    </section>
    <div class="container-ern content-wrap"><a class="btn btn-primary" href="{{ route('members.profile') }}">My details</a></div>
@endsection
