{{-- Pied commun aux cartes de répartition : rappelle que les données portent sur
     l'exercice fiscal courant et propose de calibrer une autre période.
     La page/route de calibrage n'est pas encore conçue (lien placeholder). --}}
<div class="card-footer bg-body-tertiary py-2 d-flex flex-between-center">
    <span class="fs-11 text-600">
        <span class="fas fa-circle-info me-1 text-primary"></span>Exercice {{ $indicateurs['exercice_annee'] ?? '—' }} (courant)
    </span>
    <a class="btn btn-link btn-sm p-0 fs-10 fw-medium text-decoration-none" href="#"
       title="Choisir un exercice ou un intervalle de dates (à venir)">
        Calibrer la période<span class="fas fa-sliders-h ms-1 fs-11"></span>
    </a>
</div>
