@props(['mark' => null, 'name', 'label', 'value' => null, 'help' => null, 'rows' => 6, 'id' => null])
@php
    $id = $id ?? 'f-'.$name;
    $error = $errors->first($name);
    // "key_dates.0.label" (for errors) is sent as "key_dates[0][label]".
    $htmlName = str_contains($name, '.') ? preg_replace('/\.([^.]+)/', '[$1]', $name) : $name;
    $describedBy = trim(($help ? $id.'-help ' : '').($error ? $id.'-err' : ''));
@endphp
<label class="x-field" for="{{ $id }}">
    <span>{{ $label }}@if ($mark === 'required') <span class="x-req" aria-hidden="true">*</span>@elseif ($mark === 'optional') <small>(optional)</small>@endif</span>
    <textarea class="x-input" id="{{ $id }}" name="{{ $htmlName }}" rows="{{ $rows }}"
        @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
        @if ($error) aria-invalid="true" @endif
        @if ($mark === 'required') aria-required="true" @endif
        {{ $attributes }}>{{ $value }}</textarea>
    @if ($help)
        <small id="{{ $id }}-help">{{ $help }}</small>
    @endif
    @if ($error)
        <span class="x-err" id="{{ $id }}-err">{{ $error }}</span>
    @endif
</label>
