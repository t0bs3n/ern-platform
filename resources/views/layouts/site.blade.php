@php
    // The navigation of the site: [key, label, address]. The key marks the current item.
    $navigation = [
        ['news', 'News', '/news'],
        ['conferences', 'Conferences', '/conferences'],
        ['database', 'Database', '/database'],
        ['live', 'Live', '/live'],
        ['teaching', 'Teaching', '/teaching'],
        ['podcast', 'Podcast', '/podcast'],
        ['about', 'About Us', '/about'],
        ['involved', 'Get Involved', '/get-involved'],
    ];
    $current = trim($nav ?? '');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ isset($title) && $title !== '' ? $title.' | ' : '' }}{{ config('app.name') }}</title>
    @if (! empty($description))
        <meta name="description" content="{{ $description }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/ern.css') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <script src="{{ asset('js/ern.js') }}" defer></script>
</head>
<body>
<a class="x-skip" href="#main">Skip to content</a>

<header class="site-header" id="site-header">
    <div class="container-ern">
        <nav class="site-navigation" aria-label="Main">
            <a href="{{ url('/') }}" class="site-logo focus-ring"><img src="{{ asset('img/logo.png') }}" alt="Esports Research Network, home" width="104" height="48"></a>
            <div class="main-navigation">
                <ul>
                    @foreach ($navigation as [$key, $label, $href])
                        <li{!! $current === $key ? ' class="current-menu-item"' : '' !!}><a href="{{ url($href) }}"{!! $current === $key ? ' aria-current="page"' : '' !!}>{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="x-nav-right">
                <a href="{{ url('/saved') }}" class="x-savedlink"{!! $current === 'saved' ? ' aria-current="page"' : '' !!}>
                    <svg class="x-ic" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path></svg>
                    <span class="x-vh">Saved library</span>
                </a>
                <a href="{{ url('/members') }}" class="btn btn-primary x-btn-sm"{!! $current === 'members' ? ' aria-current="page"' : '' !!}>Member area</a>
            </div>
            <a href="#mobile-menu" class="mobile-menu-toggle" id="mobile-menu-toggle" role="button" aria-label="Menu" aria-expanded="false" aria-controls="mobile-menu">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </a>
        </nav>
        <div class="mobile-menu" id="mobile-menu">
            <ul>
                @foreach ($navigation as [$key, $label, $href])
                    <li{!! $current === $key ? ' class="current-menu-item"' : '' !!}><a href="{{ url($href) }}"{!! $current === $key ? ' aria-current="page"' : '' !!}>{{ $key === 'teaching' ? 'Teaching library' : $label }}</a></li>
                @endforeach
                <li{!! $current === 'saved' ? ' class="current-menu-item"' : '' !!}><a href="{{ url('/saved') }}"{!! $current === 'saved' ? ' aria-current="page"' : '' !!}>Saved library</a></li>
                <li{!! $current === 'members' ? ' class="current-menu-item"' : '' !!}><a href="{{ url('/members') }}"{!! $current === 'members' ? ' aria-current="page"' : '' !!}>Member area</a></li>
            </ul>
        </div>
    </div>
</header>

<main class="site-main" id="main" tabindex="-1">
    @yield('content')
</main>

@include('partials.footer')
</body>
</html>
