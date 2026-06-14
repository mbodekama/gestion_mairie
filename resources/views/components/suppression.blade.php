{{--
    Bouton + modal de suppression logique avec motif obligatoire.

    <x-suppression
        :action="route('contribuables.destroy', $contribuable)"
        :bloquee="$suppressionBloquee"
        raison="Rattaché à des impositions / recouvrements."
        libelle="ce contribuable"
        id="modalSuppContrib" />
--}}
@props([
    'action',
    'bloquee'  => false,
    'raison'   => 'Suppression impossible.',
    'libelle'  => 'cet élément',
    'id'       => 'modalSuppression',
    'btnClass' => 'btn-danger btn-sm',
])

@if ($bloquee)
    <button type="button" class="btn {{ $btnClass }}" disabled
            title="{{ $raison }}" data-bs-toggle="tooltip">
        <span class="fas fa-trash me-1"></span>Supprimer
    </button>
@else
    <button type="button" class="btn {{ $btnClass }}"
            data-bs-toggle="modal" data-bs-target="#{{ $id }}">
        <span class="fas fa-trash me-1"></span>Supprimer
    </button>

    <div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ $action }}" class="modal-content">
                @csrf @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="fas fa-trash text-danger me-1"></span>Supprimer {{ $libelle }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p class="fs-9 text-muted">
                        La suppression est <strong>logique</strong> (l'élément reste dans le système, masqué) et tracée. Indiquez le motif.
                    </p>
                    <label class="form-label fs-9">Motif de la suppression <span class="text-danger">*</span></label>
                    <textarea name="motif_suppression" rows="3" maxlength="255" required
                              class="form-control @error('motif_suppression') is-invalid @enderror"
                              placeholder="Ex : doublon, erreur de saisie…">{{ old('motif_suppression') }}</textarea>
                    @error('motif_suppression') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <span class="fas fa-trash me-1"></span>Confirmer la suppression
                    </button>
                </div>
            </form>
        </div>
    </div>

    @error('motif_suppression')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                new bootstrap.Modal(document.getElementById('{{ $id }}')).show();
            });
        </script>
    @enderror
@endif
