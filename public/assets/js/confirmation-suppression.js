/*
 * Confirmation des actions via SweetAlert2.
 *
 * Remplace de façon transparente les `onsubmit="return confirm('…')"` natifs
 * par une boîte de dialogue SweetAlert, sans rien modifier dans les vues.
 *
 * Fonctionnement :
 *  - écoute l'évènement « submit » en phase de CAPTURE (avant le onsubmit inline) ;
 *  - sur un formulaire portant un onsubmit contenant confirm(), on bloque l'envoi
 *    et on neutralise le confirm() natif (stopImmediatePropagation) ;
 *  - on extrait le message d'origine pour le réafficher dans SweetAlert ;
 *  - les formulaires de suppression (méthode DELETE) reçoivent un style « danger » ;
 *  - à la validation, on soumet le formulaire via submit() (qui ne redéclenche
 *    pas le onsubmit), donc aucune double confirmation.
 *
 * Les flux de suppression à motif obligatoire (composant x-suppression, modales
 * d'annulation) n'utilisent pas confirm() : ils ne sont donc pas interceptés.
 */
(function () {
    'use strict';

    if (typeof Swal === 'undefined') {
        return;
    }

    // Extrait le message d'un attribut onsubmit du type : return confirm('…')
    function extraireMessage(onsubmit) {
        var m = onsubmit.match(/confirm\(\s*(['"])((?:\\.|(?!\1)[\s\S])*)\1/);
        if (!m) {
            return null;
        }
        // Dé-échappe \' \" \\ et les sauts de ligne éventuels
        return m[2]
            .replace(/\\(['"\\])/g, '$1')
            .replace(/\\n/g, ' ')
            .trim();
    }

    function estSuppression(form) {
        var champ = form.querySelector('input[name="_method"]');
        return champ && String(champ.value).toUpperCase() === 'DELETE';
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;

        if (!(form instanceof HTMLFormElement) || form.dataset.swalConfirme === '1') {
            return;
        }

        var onsubmit = form.getAttribute('onsubmit') || '';
        if (!/confirm\s*\(/.test(onsubmit)) {
            return; // pas de confirmation native : on laisse passer
        }

        // On bloque l'envoi ET le onsubmit inline (capture + stopImmediatePropagation)
        e.preventDefault();
        e.stopImmediatePropagation();

        var suppression = estSuppression(form);
        var message = extraireMessage(onsubmit)
            || (suppression ? 'Confirmer la suppression de cet élément ?' : 'Confirmer cette action ?');

        Swal.fire({
            title: suppression ? 'Confirmer la suppression' : 'Confirmation',
            text: message,
            icon: 'warning',
            iconColor: suppression ? '#e63757' : '#2c7be5',
            showCancelButton: true,
            reverseButtons: true,
            focusCancel: true,
            confirmButtonText: suppression
                ? '<span class="fas fa-trash me-1"></span>Oui, supprimer'
                : 'Confirmer',
            cancelButtonText: 'Annuler',
            confirmButtonColor: suppression ? '#e63757' : '#2c7be5',
            cancelButtonColor: '#748194',
            buttonsStyling: true,
        }).then(function (result) {
            if (result.isConfirmed) {
                form.dataset.swalConfirme = '1';
                form.removeAttribute('onsubmit');
                form.submit(); // ne redéclenche pas le onsubmit
            }
        });
    }, true); // true = phase de capture
})();
