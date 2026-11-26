@php $editing = $page->exists; @endphp
@extends('admin.layout', ['tab' => 'content', 'pageTitle' => ($editing ? 'Edit page' : 'New page').' | Admin area'])

@section('panel')
    <div class="x-cols">
        <form class="x-card x-stack" style="gap:1rem" method="POST" novalidate
              action="{{ $editing ? route('admin.content.pages.update', $page) : route('admin.content.pages.store') }}">
            @csrf
            @if ($editing) @method('PUT') @endif
            <div class="x-row x-between">
                <h2 class="x-card-h" style="margin:0">{{ $editing ? 'Edit page' : 'New page' }}</h2>
                <a class="x-link" href="{{ route('admin.tab', 'content') }}">Back to the content</a>
            </div>

            <x-field name="title" label="Title" :value="old('title', $page->title)" required />
            @if ($editing)
                <p class="x-small x-muted" style="margin:0">Address: /{{ $page->slug }}. It stays fixed, so that links keep working.</p>
            @else
                <x-field name="slug" label="Address" :value="old('slug')" help="Small letters, digits and hyphens, for example code-of-conduct. It cannot be changed later." required />
            @endif
            <x-textarea name="body" label="Text" :value="old('body', $page->body)" rows="20" help="Markdown: ## for a heading, **bold**, *italic*, - for a list, [text](address) for a link. HTML is shown as plain text." required />

            <div class="x-row">
                <button class="btn btn-primary" type="submit">Save</button>
                <button class="btn btn-outline" type="submit" name="preview" value="1">Preview</button>
            </div>
        </form>

        @isset($preview)
            <section class="x-card" aria-labelledby="preview-h">
                <h2 class="x-card-h" id="preview-h">Preview <small class="x-muted">not saved</small></h2>
                <article>
                    <h3>{{ $preview->title }}</h3>
                    <div class="entry-content">{{ $preview->bodyHtml() }}</div>
                </article>
            </section>
        @endisset
    </div>
@endsection
