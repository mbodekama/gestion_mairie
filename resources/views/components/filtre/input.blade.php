@props([
    'name',
    'label',
    'placeholder' => '',
    'type'        => 'text',
    'value'       => null,
    'col'         => 'col-md-3',
])

@php $valeur = $value ?? request($name); @endphp

<div class="{{ $col }}">
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    <input type="{{ $type }}"
           id="{{ $name }}"
           name="{{ $name }}"
           class="form-control form-control-lg"
           placeholder="{{ $placeholder }}"
           value="{{ $valeur }}"
           {{ $attributes }}>
</div>
