@props([
    'name',
    'label',
    'placeholder' => 'jj/mm/aaaa',
    'value'       => null,
    'col'         => 'col-md-3',
])

{{-- Champ date des filtres : s'appuie sur le composant générique x-date-picker
     (même plugin flatpickr et même init que partout ailleurs). --}}
<x-date-picker :name="$name"
               :label="$label"
               :value="$value"
               :placeholder="$placeholder"
               :col="$col" />
