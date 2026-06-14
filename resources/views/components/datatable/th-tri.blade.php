@props([
    'colonne',
    'label',
    'sortActuel'   => null,
    'dirActuelle'  => 'asc',
    'class'        => '',
])
@php
    $actif       = $sortActuel === $colonne;
    $prochainDir = ($actif && $dirActuelle === 'asc') ? 'desc' : 'asc';
    $url         = request()->fullUrlWithQuery(['sort' => $colonne, 'dir' => $prochainDir]);

    $icone = $actif
        ? ($dirActuelle === 'asc' ? 'fa-sort-up text-primary' : 'fa-sort-down text-primary')
        : 'fa-sort text-muted opacity-50';
@endphp
<th class="{{ $class }}" style="white-space: nowrap;">
    <a href="{{ $url }}" class="text-decoration-none text-reset d-inline-flex align-items-center gap-1 user-select-none">
        {{ $label }}
        <i class="fas {{ $icone }} small"></i>
    </a>
</th>
