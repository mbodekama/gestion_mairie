@props(['model'])

@php
    use App\Models\DocType;
    $typesDocuments = DocType::pourModele(get_class($model));
    $documentsExistants = $model->documents()->with('docType')->get();
    $modelType = get_class($model);
    $modelId   = $model->getKey();
@endphp

<div class="card mt-3">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 text-700">
            <span class="fas fa-paperclip me-2 text-primary"></span>
            Pièces jointes
            @if ($documentsExistants->isNotEmpty())
                <span class="badge bg-secondary ms-2">{{ $documentsExistants->count() }}</span>
            @endif
        </h6>
    </div>

    {{-- Liste des documents existants --}}
    @if ($documentsExistants->isNotEmpty())
        <div class="card-body border-bottom p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <th style="width:2rem;"></th>
                            <th>Type</th>
                            <th>Nom / Description</th>
                            <th>Fichier</th>
                            <th>Taille</th>
                            <th>Ajouté le</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documentsExistants as $doc)
                            <tr>
                                <td class="text-center"><span class="{{ $doc->icone() }}"></span></td>
                                <td>{{ $doc->docType?->libelle ?? '—' }}</td>
                                <td class="fw-semi-bold">{{ $doc->nom }}</td>
                                <td class="text-600">{{ $doc->nom_original }}</td>
                                <td class="text-600">{{ $doc->tailleListible() }}</td>
                                <td class="text-600">{{ $doc->created_at?->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('documents.telecharger', $doc) }}"
                                           class="btn btn-sm btn-outline-primary py-0" title="Télécharger">
                                            <span class="fas fa-download"></span>
                                        </a>
                                        <form method="POST" action="{{ route('documents.destroy', $doc) }}"
                                              onsubmit="return confirm('Supprimer ce document ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0" title="Supprimer">
                                                <span class="fas fa-trash"></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Formulaire d'ajout --}}
    <div class="card-body">
        <h6 class="text-700 mb-3 fs-9">
            <span class="fas fa-upload me-1 text-primary"></span>Ajouter un document
        </h6>

        @if ($typesDocuments->isEmpty())
            <p class="text-500 fs-9 mb-0">Aucun type de document configuré pour ce module.</p>
        @else
            @if (session('success'))
                <div class="alert alert-success alert-dismissible py-2 fs-9" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('documents.store') }}"
                  enctype="multipart/form-data" class="row g-3 align-items-end">
                @csrf
                <input type="hidden" name="model_type" value="{{ $modelType }}">
                <input type="hidden" name="model_id"   value="{{ $modelId }}">

                {{-- Type de document --}}
                <div class="col-md-3">
                    <label class="form-label fs-9 mb-1">Type de document <span class="text-danger">*</span></label>
                    <select name="doc_type_id" class="form-select form-select-sm @error('doc_type_id') is-invalid @enderror" required>
                        <option value="">— Choisir —</option>
                        @foreach ($typesDocuments as $type)
                            <option value="{{ $type->id }}" {{ old('doc_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->libelle }}{{ $type->obligatoire ? ' *' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('doc_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nom / description --}}
                <div class="col-md-3">
                    <label class="form-label fs-9 mb-1">Libellé <span class="text-danger">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}"
                           class="form-control form-control-sm @error('nom') is-invalid @enderror"
                           placeholder="Ex : CNI recto-verso" maxlength="255" required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Fichier --}}
                <div class="col-md-4">
                    <label class="form-label fs-9 mb-1">Fichier <span class="text-danger">*</span></label>
                    <input type="file" name="fichier"
                           class="form-control form-control-sm @error('fichier') is-invalid @enderror"
                           accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.odt,.ods" required>
                    @error('fichier')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text fs-10">PDF, images, Word, Excel — 10 Mo max</div>
                </div>

                {{-- Bouton --}}
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <span class="fas fa-upload me-1"></span>Joindre
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
