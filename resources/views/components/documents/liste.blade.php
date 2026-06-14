@props(['model'])

@php
    $documents = $model->documents()->with('docType')->get();
@endphp

<div class="card mt-3">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 text-700">
            <span class="fas fa-paperclip me-2 text-primary"></span>
            Pièces jointes
            <span class="badge bg-secondary ms-2">{{ $documents->count() }}</span>
        </h6>
    </div>

    @if ($documents->isEmpty())
        <div class="card-body text-center py-5 text-500">
            <span class="fas fa-paperclip fa-2x mb-2 d-block opacity-50"></span>
            Aucun document associé
        </div>
    @else
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped mb-0 fs-9">
                    <thead class="table-light">
                        <tr>
                            <th style="width:2rem;"></th>
                            <th>Type</th>
                            <th>Nom / Description</th>
                            <th>Fichier original</th>
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
                                <td class="text-600">{{ $doc->created_at?->format('d/m/Y à H:i') ?? '—' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('documents.telecharger', $doc) }}"
                                           class="btn btn-sm btn-primary py-0 px-2" title="Télécharger">
                                            <span class="fas fa-download me-1"></span>Télécharger
                                        </a>
                                        <form method="POST" action="{{ route('documents.destroy', $doc) }}"
                                              onsubmit="return confirm('Supprimer ce document définitivement ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger py-0 px-2" title="Supprimer">
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
</div>
