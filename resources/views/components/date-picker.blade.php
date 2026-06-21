@props([
    'name',
    'label'      => null,
    'value'      => null,          // Carbon, 'Y-m-d' ou 'd/m/Y'
    'placeholder'=> 'jj/mm/aaaa',
    'col'        => 'col-md-3',
    'required'   => false,
    'min'        => null,          // borne flatpickr (Carbon / 'Y-m-d' / 'd/m/Y')
    'max'        => null,
    'id'         => null,
    'size'       => 'form-control-lg',
    'labelClass' => '',
])

@php
    /**
     * Normalise une valeur date hétérogène vers le format d'affichage d/m/Y
     * attendu par flatpickr (et posté tel quel : validé en date_format:d/m/Y).
     */
    $versAffichage = static function ($v): string {
        if ($v instanceof \DateTimeInterface) {
            return $v->format('d/m/Y');
        }
        if (is_string($v) && preg_match('#^\d{2}/\d{2}/\d{4}$#', $v)) {
            return $v;
        }
        return filled($v) ? \Illuminate\Support\Carbon::parse($v)->format('d/m/Y') : '';
    };

    $idChamp = $id ?? $name;
    $valeur  = $versAffichage(old($name, $value ?? request($name)));
    $minAff  = $min ? $versAffichage($min) : null;
    $maxAff  = $max ? $versAffichage($max) : null;
@endphp

<div class="{{ $col }}">
    @if ($label)
        <label class="form-label {{ $labelClass }}" for="{{ $idChamp }}">
            {{ $label }}@if ($required) <span class="text-danger">*</span>@endif
        </label>
    @endif

    <input type="text"
           id="{{ $idChamp }}"
           name="{{ $name }}"
           class="form-control {{ $size }} date-picker @error($name) is-invalid @enderror"
           placeholder="{{ $placeholder }}"
           autocomplete="off"
           @if ($required) required @endif
           @if ($minAff) data-min="{{ $minAff }}" @endif
           @if ($maxAff) data-max="{{ $maxAff }}" @endif
           value="{{ $valeur }}">

    @error($name) <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

@once
    @push('styles')
        <link href="{{ asset('vendors/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
    @endpush
    @push('scripts')
        <script src="{{ asset('vendors/flatpickr/flatpickr.min.js') }}"></script>
        <script>
            window.fiscctFlatpickrLocale = {
                firstDayOfWeek: 1,
                weekdays: {
                    shorthand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
                    longhand:  ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
                },
                months: {
                    shorthand: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                    longhand:  ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                },
            };
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.date-picker').forEach(function (el) {
                    if (el._flatpickr) return; // déjà initialisé
                    flatpickr(el, {
                        dateFormat: 'd/m/Y',
                        allowInput: true,
                        locale:     window.fiscctFlatpickrLocale,
                        minDate:    el.dataset.min || null,
                        maxDate:    el.dataset.max || null,
                    });
                });
            });
        </script>
    @endpush
@endonce
