@props(['mark' => null, 'name', 'label', 'options' => [], 'value' => null, 'help' => null, 'placeholder' => 'Please choose', 'id' => null])
@php
    $id = $id ?? 'f-'.$name;
    $error = $errors->first($name);
    $describedBy = trim(($help ? $id.'-help ' : '').($error ? $id.'-err' : ''));
@endphp
<label class="x-field" for="{{ $id }}">
    <span>{{ $label }}@if ($mark === 'required') <span class="x-req" aria-hidden="true">*</span>@elseif ($mark === 'optional') <small>(optional)</small>@endif</span>
    <select class="x-input" id="{{ $id }}" name="{{ $name }}"
        @if ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
        @if ($error) aria-invalid="true" @endif
        @if ($mark === 'required') aria-required="true" @endif
        {{ $attributes }}>
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $option)
            <option value="{{ $option }}" @selected($value === $option)>{{ $option }}</option>
        @endforeach
    </select>
    @if ($help)
        <small id="{{ $id }}-help">{{ $help }}</small>
    @endif
    @if ($error)
        <span class="x-err" id="{{ $id }}-err">{{ $error }}</span>
    @endif
</label>
