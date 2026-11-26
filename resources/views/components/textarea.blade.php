@props(['name', 'label', 'value' => null, 'help' => null, 'rows' => 6, 'id' => null])
@php
    $id = $id ?? 'f-'.$name;
    $error = $errors->first($name);
    $describedBy = trim(($help ? $id.'-help ' : '').($error ? $id.'-err' : ''));
@endphp
<label class="x-field" for="{{ $id }}">
    <span>{{ $label }}</span>
    <textarea class="x-input" id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
        @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
        @if ($error) aria-invalid="true" @endif
        {{ $attributes }}>{{ $value }}</textarea>
    @if ($help)
        <small id="{{ $id }}-help">{{ $help }}</small>
    @endif
    @if ($error)
        <span class="x-err" id="{{ $id }}-err">{{ $error }}</span>
    @endif
</label>
