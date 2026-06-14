@props([
    'historiques',
    'titre'      => 'Historique des modifications',
    'labels'     => [],
    'creeAt'     => null,
    'misAJourAt' => null,
])

@php
    use App\Models\HistoriqueModification;
    $total = $historiques->count();
@endphp

<div class="card mt-3">
    <div class="card-header py-3">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-history me-2 text-primary"></span>{{ $titre }}
            </h5>
            @if ($total > 0)
                <span class="badge bg-primary rounded-pill">{{ $total }}</span>
            @endif
        </div>

        @if ($creeAt || $misAJourAt)
            <div class="d-flex gap-4 mt-2 fs-10 text-600">
                @if ($creeAt)
                    <span>
                        <span class="fas fa-calendar-plus me-1 text-success"></span>
                        Créé le <strong class="text-700">{{ $creeAt->format('d/m/Y à H:i') }}</strong>
                    </span>
                @endif
                @if ($misAJourAt)
                    <span>
                        <span class="fas fa-calendar-check me-1 text-warning"></span>
                        Dernière modification <strong class="text-700">{{ $misAJourAt->format('d/m/Y à H:i') }}</strong>
                        <span class="text-400 ms-1">({{ $misAJourAt->diffForHumans() }})</span>
                    </span>
                @endif
            </div>
        @endif
    </div>

    <div class="card-body p-0">
        @forelse ($historiques as $entree)
            @php
                [$icone, $couleur, $libelle] = match ($entree->evenement) {
                    HistoriqueModification::CREATION     => ['fas fa-plus-circle', 'success', 'Création'],
                    HistoriqueModification::SUPPRESSION  => ['fas fa-trash-circle', 'danger', 'Suppression'],
                    default                              => ['fas fa-pencil-alt',   'warning', 'Modification'],
                };

                $avant = $entree->donnees_avant ?? [];
                $apres = $entree->donnees_apres ?? [];

                // Pour une MODIFICATION : seuls les champs changés (apres contient les nouvelles valeurs)
                // Pour une CREATION/SUPPRESSION : tous les champs de apres/avant
                $champs = array_keys($apres ?: $avant);
            @endphp

            <div class="d-flex gap-3 px-4 py-3 border-bottom align-items-start hover-bg-soft-primary">

                {{-- Icône événement --}}
                <div class="flex-shrink-0 mt-1">
                    <span class="fas {{ str_replace('fas ', '', $icone) }} text-{{ $couleur }}" style="font-size:1rem;"></span>
                </div>

                {{-- Contenu --}}
                <div class="flex-grow-1 fs-9">
                    {{-- Titre --}}
                    <p class="mb-1 fw-semi-bold text-900">
                        {{ $libelle }}
                    </p>

                    {{-- Auteur + date --}}
                    <p class="mb-2 text-600">
                        <span class="fas fa-user me-1"></span>{{ $entree->utilisateur_nom ?? 'Système' }}
                        <span class="mx-2 text-300">·</span>
                        <span class="fas fa-calendar me-1"></span>{{ $entree->created_at?->format('d/m/Y à H:i') ?? '—' }}
                    </p>

                    {{-- Détail des champs --}}
                    @if (!empty($champs))
                        <div class="d-flex flex-wrap gap-1">
                            @foreach ($champs as $champ)
                                @php
                                    $valAvant = $avant[$champ] ?? null;
                                    $valApres = $apres[$champ] ?? null;
                                    $label    = $labels[$champ] ?? $champ;
                                @endphp

                                @if ($entree->evenement === HistoriqueModification::MODIFICATION)
                                    {{-- Diff avant → après --}}
                                    <span class="badge border fs-10 fw-normal" style="background:#f8f9fa; color:#495057;">
                                        <span class="text-600">{{ $label }}:</span>
                                        @if ($valAvant !== null)
                                            <span class="text-danger text-decoration-line-through ms-1">{{ Str::limit((string) $valAvant, 40) }}</span>
                                            <span class="text-500 mx-1">→</span>
                                        @endif
                                        <span class="text-success ms-1">{{ Str::limit((string) $valApres, 40) }}</span>
                                    </span>
                                @else
                                    {{-- Création / Suppression : valeur seule --}}
                                    @php $val = $valApres ?? $valAvant; @endphp
                                    @if ($val !== null && $val !== '')
                                        <span class="badge border fs-10 fw-normal" style="background:#f8f9fa; color:#495057;">
                                            <span class="text-600">{{ $label }}:</span>
                                            <span class="{{ $entree->evenement === HistoriqueModification::CREATION ? 'text-success' : 'text-danger' }} ms-1">{{ Str::limit((string) $val, 40) }}</span>
                                        </span>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Temps relatif --}}
                <div class="flex-shrink-0 text-600 fs-10 text-nowrap mt-1">
                    {{ $entree->created_at?->diffForHumans() ?? '' }}
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-500">
                <span class="fas fa-history fa-2x mb-2 d-block opacity-50"></span>
                Aucune modification enregistrée
            </div>
        @endforelse
    </div>
</div>
