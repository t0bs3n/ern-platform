@php $editing = $news->exists; @endphp
@extends('admin.layout', ['tab' => 'content', 'pageTitle' => ($editing ? 'Edit post' : 'New post').' | Admin area'])

@section('panel')
    <div class="x-cols">
        <form class="x-card x-stack" style="gap:1rem" method="POST" enctype="multipart/form-data" novalidate
              action="{{ $editing ? route('admin.content.news.update', $news) : route('admin.content.news.store') }}">
            @csrf
            @if ($editing) @method('PUT') @endif
            <div class="x-row x-between">
                <h2 class="x-card-h" style="margin:0">{{ $editing ? 'Edit post' : 'New post' }}</h2>
                <a class="x-link" href="{{ route('admin.tab', 'content') }}">Back to the content</a>
            </div>

            <x-field name="title" label="Title" :value="old('title', $news->title)" required />
            <x-field name="category" label="Category" :value="old('category', $news->category)" help="Optional, for example News or ERNC27." />
            <x-textarea name="summary" label="Summary" :value="old('summary', $news->summary)" rows="3" help="Two or three sentences for the news list and the start page." required />
            <x-textarea name="body" label="Text" :value="old('body', $news->body)" rows="14" help="Markdown: ## for a heading, **bold**, *italic*, - for a list, [text](address) for a link. HTML is shown as plain text." />

            <fieldset class="x-fieldset">
                <legend>Image <small>Optional. JPG, PNG or WebP, at most 2 MB.</small></legend>
                @if ($editing && $news->image_path)
                    <img src="{{ route('news.image', $news) }}" alt="{{ $news->image_alt }}" style="max-width:16rem;border-radius:.5rem">
                    <label class="x-check" for="f-remove_image"><input type="checkbox" id="f-remove_image" name="remove_image" value="1" @checked(old('remove_image'))><span>Remove the image</span></label>
                @endif
                <x-field name="image" label="{{ $editing && $news->image_path ? 'Replace the image' : 'Choose an image' }}" type="file" accept="image/jpeg,image/png,image/webp" />
                <x-field name="image_alt" label="Image description" :value="old('image_alt', $news->image_alt)" help="What the image shows, for people who cannot see it." />
            </fieldset>

            <fieldset class="x-fieldset">
                <legend>Status</legend>
                @foreach ([\App\Models\News::DRAFT => 'Draft: only editors see it', \App\Models\News::PUBLISHED => 'Published: everyone sees it from the date below'] as $value => $text)
                    <label class="x-check" for="f-status-{{ $value }}"><input type="radio" id="f-status-{{ $value }}" name="status" value="{{ $value }}" @checked(old('status', $news->status ?? \App\Models\News::DRAFT) === $value)><span>{{ $text }}</span></label>
                @endforeach
                @error('status') <span class="x-err">{{ $message }}</span> @enderror
            </fieldset>
            <x-field name="published_at" label="Date" type="date" :value="old('published_at', $news->published_at?->format('Y-m-d'))" help="Leave empty to use today when publishing." />

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
                    <p class="x-muted">{{ $preview->summary }}</p>
                    <div class="entry-content">{{ $preview->bodyHtml() }}</div>
                </article>
            </section>
        @endisset
    </div>
@endsection
