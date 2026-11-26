@props(['name', 'label', 'type' => 'text', 'value' => null, 'help' => null, 'autocomplete' => null, 'placeholder' => null, 'id' => null])
@php
    $id = $id ?? 'f-'.$name;
    $error = $errors->first($name);
    $describedBy = trim(($help ? $id.'-help ' : '').($error ? $id.'-err' : ''));
@endphp
<label class="x-field" for="{{ $id }}">
    <span>{{ $label }}</span>
    <input class="x-input" id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
        @if ($value !== null) value="{{ $value }}" @endif
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
        @if ($error) aria-invalid="true" @endif
        {{ $attributes }}>
    @if ($help)
        <small id="{{ $id }}-help">{{ $help }}</small>
    @endif
    @if ($error)
        <span class="x-err" id="{{ $id }}-err">{{ $error }}</span>
    @endif
</label>
