<x-app-layout :title="__('Mails groupés')">

    <x-page-header titre="Campagnes de mails groupés"
        sousTitre="Historique des campagnes planifiées et envoyées aux contribuables." />

    {{-- ===== Card Filtres ===== --}}
    <x-filtre.card :action="route('contribuables.mails-groupes.recherche')" :reset="route('contribuables.mails-groupes.index')"
        titre="Filtrer les campagnes selon vos critères">
        <x-filtre.input name="objet" label="Sujet du mail" placeholder="Recherche par sujet..." />
        <x-filtre.date name="date_debut" label="Date prévue (du)" />
        <x-filtre.date name="date_fin" label="Date prévue (au)" />
        <x-filtre.select name="statut" label="Statut">
            <option value="EN_ATTENTE" @selected(request('statut') === 'EN_ATTENTE')>En attente</option>
            <option value="EN_COURS"   @selected(request('statut') === 'EN_COURS')>En cours</option>
            <option value="ENVOYE"     @selected(request('statut') === 'ENVOYE')>Envoyé</option>
            <option value="ECHEC"      @selected(request('statut') === 'ECHEC')>Échec</option>
        </x-filtre.select>
    </x-filtre.card>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <span class="fas fa-paper-plane me-2 text-primary"></span>
                Campagnes
                <span class="badge bg-secondary ms-2">{{ $campagnes->total() }}</span>
            </h5>
            @can('CONTRIB_MAILS')
            <a href="{{ route('contribuables.mails-groupes.create') }}" class="btn btn-primary">
                <span class="fas fa-plus me-1"></span>Nouvelle campagne
            </a>
            @endcan
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <th>N° d'ordre</th>
                            <th class="text-center">Contribuables ciblés</th>
                            <th>Sujet du mail</th>
                            <th>Date de planification</th>
                            <th>Date prévue d'envoi</th>
                            <th class="text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($campagnes as $campagne)
                            <tr>
                                <td class="fw-semi-bold">{{ $campagne->numero }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $campagne->nombre_cibles }}</span>
                                    @if ($campagne->statut === \App\Models\CampagneMail::STATUT_ENVOYE && $campagne->nombre_envoyes !== $campagne->nombre_cibles)
                                        <span class="text-muted small d-block">{{ $campagne->nombre_envoyes }} envoyé(s)</span>
                                    @endif
                                </td>
                                <td>{{ $campagne->objet }}</td>
                                <td>{{ $campagne->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td>{{ $campagne->date_envoi_prevue?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $campagne->statutClasse() }}">{{ $campagne->statutLibelle() }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                    Aucune campagne pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>N° d'ordre</th>
                            <th class="text-center">Contribuables ciblés</th>
                            <th>Sujet du mail</th>
                            <th>Date de planification</th>
                            <th>Date prévue d'envoi</th>
                            <th class="text-center">Statut</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                @if ($campagnes->total() > 0)
                    Affichage de {{ $campagnes->firstItem() }} à {{ $campagnes->lastItem() }}
                    sur {{ $campagnes->total() }} campagne(s)
                @else
                    Aucune campagne à afficher
                @endif
            </small>
            @if ($campagnes->hasPages())
                {{ $campagnes->links() }}
            @endif
        </div>
    </div>

</x-app-layout>
