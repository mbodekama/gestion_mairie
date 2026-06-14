@props([
    'name',
    'label',
    'placeholder' => 'jj/mm/aaaa',
    'value'       => null,
    'col'         => 'col-md-3',
])

@php
    $raw    = $value ?? request($name);
    $valeur = $raw ? \Carbon\Carbon::parse($raw)->format('d/m/Y') : '';
@endphp

<div class="{{ $col }}">
    <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    <input type="text"
           id="{{ $name }}"
           name="{{ $name }}"
           class="form-control form-control-lg date-picker"
           placeholder="{{ $placeholder }}"
           autocomplete="off"
           value="{{ $valeur }}">
</div>

@once
    @push('styles')
        <link href="{{ asset('vendors/flatpickr/flatpickr.min.css') }}" rel="stylesheet">
    @endpush
    @push('scripts')
        <script src="{{ asset('vendors/flatpickr/flatpickr.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const locale = {
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
                document.querySelectorAll('.date-picker').forEach(function (el) {
                    flatpickr(el, { dateFormat: 'd/m/Y', allowInput: true, locale });
                });
            });
        </script>
    @endpush
@endonce
