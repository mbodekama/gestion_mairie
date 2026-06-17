{{--
    Composant universel de gestion des pièces jointes.

    Pré-requis sur le modèle cible :
        use App\Traits\HasDocuments;

    Usage show  (lecture seule + téléchargement) :
        <x-documents.panneau :model="$contribuable" />

    Usage edit  (liste + formulaire d'upload) :
        <x-documents.panneau :model="$contribuable" :editable="true" />
--}}
@props(['model', 'editable' => false, 'numero' => null])

@php
    use App\Models\DocType;

    if (! method_exists($model, 'documents')) {
        throw new \LogicException(get_class($model) . ' doit utiliser le trait HasDocuments.');
    }

    // Resilient : la table peut ne pas exister si la migration n'a pas encore été jouée
    try {
        $documents      = $model->documents()->with('docType')->get();
        $typesDocuments = $editable ? DocType::pourModele(get_class($model)) : collect();
    } catch (\Illuminate\Database\QueryException) {
        $documents      = collect();
        $typesDocuments = collect();
    }
    $modelType      = get_class($model);
    $modelId        = $model->getKey();
@endphp

<div class="card mt-3 @if ($numero) card-section @endif">

    {{-- ── En-tête ──────────────────────────────────────────────────── --}}
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h5 class="mb-0 d-flex align-items-center">
            @if ($numero)
                <span class="num-section">{{ $numero }}</span>
            @endif
            <span class="fas fa-paperclip me-2 text-primary"></span>
            Pièces jointes
            <span class="badge bg-secondary ms-2">{{ $documents->count() }}</span>
        </h5>
    </div>

    {{-- ── Liste des documents ─────────────────────────────────────── --}}
    @if ($documents->isEmpty())
        <div class="card-body text-center py-4 text-500 fs-9 @if ($editable) border-bottom @endif">
            <span class="fas fa-paperclip fa-2x mb-2 d-block opacity-40"></span>
            Aucun document associé
        </div>
    @else
        <div class="card-body p-0 @if ($editable) border-bottom @endif">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <th style="width:2rem;"></th>
                            <th>Type</th>
                            <th>Nom / Description</th>
                            <th>Fichier</th>
                            <th>Taille</th>
                            <th>Ajouté par</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $doc)
                            <tr>
                                <td class="text-center ps-3">
                                    <span class="{{ $doc->icone() }} fa-lg"></span>
                                </td>
                                <td>
                                    <span class="badge bg-soft-primary text-primary border border-primary fs-10">
                                        {{ $doc->docType?->libelle ?? '—' }}
                                    </span>
                                </td>
                                <td class="fw-semi-bold text-900">{{ $doc->nom }}</td>
                                <td class="text-600">{{ $doc->nom_original }}</td>
                                <td class="text-600">{{ $doc->tailleListible() }}</td>
                                <td class="text-600">{{ $doc->uploaded_by_nom ?? '—' }}</td>
                                <td class="text-600">{{ $doc->created_at?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('documents.telecharger', $doc) }}"
                                           class="btn btn-sm btn-primary py-0 px-2"
                                           title="Télécharger {{ $doc->nom_original }}">
                                            <span class="fas fa-download me-1"></span>Télécharger
                                        </a>
                                        @if ($editable)
                                            <form method="POST" action="{{ route('documents.destroy', $doc) }}"
                                                  onsubmit="return confirm('Supprimer « {{ addslashes($doc->nom) }} » définitivement ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger py-0 px-2"
                                                        title="Supprimer">
                                                    <span class="fas fa-trash"></span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ── Formulaire d'upload (mode édition uniquement) ───────────── --}}
    @if ($editable)
        <div class="card-body">
            <h6 class="text-700 mb-3 fs-9">
                <span class="fas fa-upload me-1 text-primary"></span>Ajouter un document
            </h6>

            @if ($typesDocuments->isEmpty())
                <p class="text-500 fs-9 mb-0">
                    Aucun type de document configuré pour ce module.
                    Ajoutez des entrées dans <code>DocTypeSeeder</code> pour {{ class_basename($modelType) }}.
                </p>
            @else

                <form method="POST" action="{{ route('documents.store') }}"
                      enctype="multipart/form-data" class="row g-3 align-items-end">
                    @csrf
                    <input type="hidden" name="model_type" value="{{ $modelType }}">
                    <input type="hidden" name="model_id"   value="{{ $modelId }}">

                    <div class="col-md-3">
                        <label class="form-label fs-9 mb-1">
                            Type de document <span class="text-danger">*</span>
                        </label>
                        <select name="doc_type_id"
                                class="form-select form-select-sm @error('doc_type_id') is-invalid @enderror"
                                required>
                            <option value="">— Choisir —</option>
                            @foreach ($typesDocuments as $type)
                                <option value="{{ $type->id }}"
                                        {{ old('doc_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->libelle }}{{ $type->obligatoire ? ' *' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('doc_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fs-9 mb-1">
                            Libellé <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nom" value="{{ old('nom') }}"
                               class="form-control form-control-sm @error('nom') is-invalid @enderror"
                               placeholder="Ex : CNI recto-verso" maxlength="255" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fs-9 mb-1">
                            Fichier <span class="text-danger">*</span>
                            <span class="text-muted fw-normal fs-10">— PDF, images, Word, Excel — 10 Mo max</span>
                        </label>
                        <input type="file" name="fichier"
                               class="form-control form-control-sm @error('fichier') is-invalid @enderror"
                               accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.odt,.ods"
                               required>
                        @error('fichier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <span class="fas fa-upload me-1"></span>Joindre
                        </button>
                    </div>
                </form>
            @endif
        </div>
    @endif

    {{-- ── Pied de carte (mode section numérotée) ───────────────────── --}}
    @if ($numero)
        <div class="card-footer d-flex justify-content-between align-items-center py-2 fs-9">
            <span class="text-600">
                <span class="fas fa-paperclip me-1"></span>{{ $documents->count() }} document(s)
            </span>
        </div>
    @endif

</div>
