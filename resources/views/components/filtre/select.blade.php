@props([
    'name',
    'label',
    'options'             => collect(),
    'optionLabel'         => 'libelle',
    'optionLabelFallback' => null,
    'optionValue'         => 'id',
    'placeholder'         => '— Tous —',
    'col'                 => 'col-md-3',
])

@php $valeur = request($name); @endphp

<div class="{{ $col }}">
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    <select id="{{ $name }}"
            name="{{ $name }}"
            class="form-select form-select-lg mb-3"
            aria-label=".form-select-lg example">
        <option value="">{{ $placeholder }}</option>

        @if ($slot->isNotEmpty())
            {{-- Options statiques passées via le slot --}}
            {{ $slot }}
        @else
            {{-- Options dynamiques depuis une collection --}}
            @foreach ($options as $option)
                @php
                    $libelle = $optionLabelFallback
                        ? (data_get($option, $optionLabel) ?? data_get($option, $optionLabelFallback))
                        : data_get($option, $optionLabel);
                    $valeurOption = data_get($option, $optionValue);
                @endphp
                <option value="{{ $valeurOption }}"
                        @selected($valeur == $valeurOption)>
                    {{ $libelle }}
                </option>
            @endforeach
        @endif
    </select>
</div>
