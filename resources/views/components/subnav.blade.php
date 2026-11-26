@props(['items'])
{{-- A row of jump links under the header on long pages. Works without JavaScript. --}}
<nav class="x-subnav" aria-label="On this page">
    <div class="container-ern">
        <span class="x-subnav-label">On this page</span>
        @foreach ($items as [$id, $label])
            <a href="#sec-{{ $id }}">{{ $label }}</a>
        @endforeach
    </div>
</nav>
