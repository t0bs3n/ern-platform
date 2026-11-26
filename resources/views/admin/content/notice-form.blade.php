@php $editing = $notice->exists; @endphp
@extends('admin.layout', ['tab' => 'content', 'pageTitle' => ($editing ? 'Edit notice' : 'New notice').' | Admin area'])

@section('panel')
    <div class="x-stack" style="max-width:40rem">
        <form class="x-card x-stack" style="gap:1rem" method="POST" novalidate
              action="{{ $editing ? route('admin.content.notices.update', $notice) : route('admin.content.notices.store') }}">
            @csrf
            @if ($editing) @method('PUT') @endif
            <div class="x-row x-between">
                <h2 class="x-card-h" style="margin:0">{{ $editing ? 'Edit notice' : 'New notice' }}</h2>
                <a class="x-link" href="{{ route('admin.tab', 'content') }}">Back to the content</a>
            </div>
            <p class="x-small x-muted" style="margin:0">Notices are for signed-in members only. Never name candidates of a board election.</p>
            <x-field name="kind" label="Label" :value="old('kind', $notice->kind)" help="For example: General Assembly, Board election, Association" />
            <x-field name="title" label="Title" :value="old('title', $notice->title)" />
            <x-textarea name="body" label="Text" rows="6" :value="old('body', $notice->body)" help="Markdown: **bold**, - for a list, [text](address) for a link." />
            <fieldset class="x-fieldset">
                <legend>Status</legend>
                @foreach ([\App\Models\Notice::DRAFT => 'Draft: only editors see it', \App\Models\Notice::PUBLISHED => 'Published: members see it'] as $value => $text)
                    <label class="x-check" for="f-status-{{ $value }}"><input type="radio" id="f-status-{{ $value }}" name="status" value="{{ $value }}" @checked(old('status', $notice->status) === $value)><span>{{ $text }}</span></label>
                @endforeach
            </fieldset>
            <div class="x-row"><button class="btn btn-primary" type="submit">Save</button></div>
        </form>
        @if ($editing)
            <form class="x-card" method="POST" action="{{ route('admin.content.notices.destroy', $notice) }}">
                @csrf
                @method('DELETE')
                <h2 class="x-card-h">Remove the notice</h2>
                <button class="btn btn-outline" type="submit">Remove {{ $notice->title }}</button>
            </form>
        @endif
    </div>
@endsection
