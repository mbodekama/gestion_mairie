<x-app-layout :title="__('Nouvelle campagne de mails')">

    <x-page-header titre="Nouvelle campagne de mails groupés"
        sousTitre="Ciblez les contribuables, composez le message et planifiez la date d'envoi." />

    <div class="mb-3">
        <a href="{{ route('contribuables.mails-groupes.index') }}" class="btn btn-sm btn-outline-secondary">
            <span class="fas fa-arrow-left me-1"></span>Retour aux campagnes
        </a>
    </div>

    {{-- ===== Card Filtres (ciblage des destinataires) ===== --}}
    <x-filtre.card :action="route('contribuables.mails-groupes.filtre')" :reset="route('contribuables.mails-groupes.create')"
        titre="Cibler les destinataires selon vos critères">
        <x-filtre.input name="numero_identifiant" label="N° Identifiant" placeholder="Ex : CI2024000123" />
        <x-filtre.input name="nom" label="Nom / Raison sociale" placeholder="Recherche par nom..." />
        <x-filtre.select name="type_personne" label="Type de personne"
            :options="$typesPersonne" option-label="libelle" option-value="code" />
        <x-filtre.select name="statut" label="Statut"
            :options="$statuts" option-label="libelle" option-value="code" />
        <x-filtre.select name="regime_imposition_id" label="Régime d'imposition"
            :options="$regimes" option-label="libelle_court" option-label-fallback="libelle" />
    </x-filtre.card>

    <div class="row g-3">
        {{-- ===== Composition + planification ===== --}}
        <div class="col-12 col-xl-5">
            <form method="POST" action="{{ route('contribuables.mails-groupes.store') }}" id="formMailGroupe"
                  onsubmit="return confirm('Planifier la campagne pour {{ $destinataires->total() }} destinataire(s) ?')">
                @csrf
                {{-- Report du ciblage courant pour que le serveur re-résolve les mêmes destinataires --}}
                <input type="hidden" name="numero_identifiant" value="{{ $filtre->numeroIdentifiant }}">
                <input type="hidden" name="nom" value="{{ $filtre->nom }}">
                <input type="hidden" name="type_personne" value="{{ $filtre->typePersonne }}">
                <input type="hidden" name="statut" value="{{ $filtre->statut }}">
                <input type="hidden" name="regime_imposition_id" value="{{ $filtre->regimeImpositionId }}">

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <span class="fas fa-envelope-open-text me-2 text-primary"></span>Composer & planifier
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fs-9" for="objet">Objet <span class="text-danger">*</span></label>
                            <input type="text" name="objet" id="objet" maxlength="150"
                                   value="{{ old('objet') }}"
                                   class="form-control @error('objet') is-invalid @enderror"
                                   placeholder="Objet de l'e-mail" required>
                            @error('objet') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-9" for="message">Message <span class="text-danger">*</span></label>
                            <textarea name="message" id="message" rows="9" maxlength="5000"
                                      class="form-control @error('message') is-invalid @enderror"
                                      placeholder="Corps du message adressé aux contribuables..."
                                      required>{{ old('message') }}</textarea>
                            @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Les sauts de ligne sont conservés. Aucune mise en forme HTML.</small>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fs-9" for="date_envoi_prevue">
                                Date prévue pour l'envoi <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" name="date_envoi_prevue" id="date_envoi_prevue"
                                   value="{{ old('date_envoi_prevue', now()->format('Y-m-d\TH:i')) }}"
                                   class="form-control @error('date_envoi_prevue') is-invalid @enderror" required>
                            @error('date_envoi_prevue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Laissez la date courante pour un envoi immédiat.</small>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <span class="fas fa-users me-1"></span>
                            {{ $destinataires->total() }} destinataire(s) avec e-mail
                        </small>
                        <button type="submit" class="btn btn-primary"
                                @disabled($destinataires->total() === 0)>
                            <span class="fas fa-paper-plane me-1"></span>Planifier la campagne
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ===== Aperçu des destinataires ===== --}}
        <div class="col-12 col-xl-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <span class="fas fa-address-book me-2 text-primary"></span>
                        Destinataires
                        <span class="badge bg-secondary ms-2">{{ $destinataires->total() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped mb-0 fs-9">
                            <thead class="table-light">
                                <tr>
                                    <th>N° Identifiant</th>
                                    <th>Nom / Raison sociale</th>
                                    <th>E-mail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($destinataires as $contribuable)
                                    <tr>
                                        <td class="fw-semi-bold">{{ $contribuable->numero_identifiant }}</td>
                                        <td>
                                            @if ($contribuable->type_personne === 'PP')
                                                {{ $contribuable->nom }} {{ $contribuable->prenoms }}
                                            @else
                                                {{ $contribuable->raison_sociale }}
                                                @if ($contribuable->sigle)
                                                    <span class="text-muted">({{ $contribuable->sigle }})</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ $contribuable->email }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            <span class="fas fa-inbox fa-2x d-block mb-2"></span>
                                            Aucun contribuable avec e-mail ne correspond à ces critères.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($destinataires->hasPages())
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Affichage de {{ $destinataires->firstItem() }} à {{ $destinataires->lastItem() }}
                            sur {{ $destinataires->total() }} destinataire(s)
                        </small>
                        {{ $destinataires->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
