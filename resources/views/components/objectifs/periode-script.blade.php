{{-- Relie le sélecteur d'exercice aux deux champs flatpickr de période :
     borne min/max sur la période de l'exercice et pré-remplit les dates vides.
     S'exécute après l'init flatpickr du composant x-date-picker (même stack). --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('exercice-select');
        const debut  = document.querySelector('input[name="periode_debut"]');
        const fin    = document.querySelector('input[name="periode_fin"]');
        if (!select || !debut || !fin) return;

        const toDate = (s) => (s ? new Date(s + 'T00:00:00') : null);

        function appliquer(prefill) {
            const opt = select.options[select.selectedIndex];
            const d   = opt ? opt.dataset.debut : '';   // Y-m-d
            const f   = opt ? opt.dataset.fin   : '';
            const fpD = debut._flatpickr;
            const fpF = fin._flatpickr;

            if (fpD) { fpD.set('minDate', toDate(d)); fpD.set('maxDate', toDate(f)); }
            if (fpF) { fpF.set('minDate', toDate(d)); fpF.set('maxDate', toDate(f)); }

            if (prefill && d && f) {
                if (!debut.value && fpD) fpD.setDate(toDate(d), true);
                if (!fin.value   && fpF) fpF.setDate(toDate(f), true);
            }
        }

        select.addEventListener('change', () => appliquer(true));
        appliquer(false); // au chargement : borne sans écraser les valeurs existantes
    });
</script>
