{{--
    Toast de bienvenue superposé à l'écran (coin haut-droit). Affiché une seule
    fois, juste après la connexion ou le déverrouillage de session, puis disparaît
    automatiquement après quelques secondes.

    Sous la salutation, un conseil d'utilisation est tiré au hasard pour rendre
    l'accueil plus vivant et utile.

    Déclenché côté contrôleur via un flash de session :
        return redirect()->route('dashboard')->with('toast_bienvenue', 'Bon retour …');
--}}
@if (session('toast_bienvenue'))
    @php
        $initiale = mb_strtoupper(mb_substr(auth()->user()->name ?? '?', 0, 1));

        // Conseils métier / prise en main, tirés au hasard à chaque connexion.
        $conseils = [
            "Vérifiez qu'un exercice fiscal est bien ouvert avant de lancer une nouvelle émission de taxe.",
            "Un règlement ne vise qu'une seule émission : soldez en priorité les émissions les plus anciennes.",
            "Utilisez les filtres puis l'export Excel pour préparer rapidement vos états de recouvrement.",
            "Le numéro de compte d'un contribuable est unique : pensez à le rechercher avant d'en créer un nouveau.",
            "Ne clôturez un exercice fiscal qu'une fois tous ses recouvrements enregistrés.",
            "La page « Statistiques mairie » offre une vue d'ensemble des émissions et recouvrements sur 12 mois.",
            "Verrouillez votre session (cadenas du menu) dès que vous quittez votre poste, même quelques minutes.",
            "Les montants suivent le barème en vigueur : vérifiez la tranche de chiffre d'affaires du contribuable.",
            "Joignez les pièces justificatives au dossier du contribuable pour un meilleur suivi.",
            "Consultez le « Top contribuables » pour identifier les principaux contributeurs au budget de la mairie.",
            "Avant d'éditer une quittance, assurez-vous que le règlement est bien encaissé et non annulé.",
            "Un suivi régulier du taux de recouvrement aide à atteindre les objectifs annuels de la collectivité.",
            "Renseignez soigneusement les contacts des contribuables : ils servent aux avis et convocations.",
        ];
        $conseil = $conseils[array_rand($conseils)];
    @endphp

    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;">
        <div id="toastBienvenue"
             class="toast border-0 shadow-lg overflow-hidden"
             style="width: 22rem; max-width: calc(100vw - 1.5rem);"
             role="alert" aria-live="assertive" aria-atomic="true"
             data-bs-autohide="true" data-bs-delay="14000">

            {{-- En-tête dégradé : salutation --}}
            <div class="d-flex align-items-center bg-primary bg-gradient text-white px-3 py-3">
                <div class="bg-white text-primary me-3 icon-item rounded-circle flex-shrink-0">
                    <span class="fw-bold fs-5">{{ $initiale }}</span>
                </div>
                <div class="flex-1 me-2">
                    <h6 class="mb-0 text-white">{{ session('toast_bienvenue') }}</h6>
                    <span class="fs--1 text-white-50">{{ now()->format('d/m/Y · H\hi') }}</span>
                </div>
                <button class="btn-close btn-close-white flex-shrink-0" type="button"
                        data-bs-dismiss="toast" aria-label="Fermer"></button>
            </div>

            {{-- Corps : conseil aléatoire --}}
            <div class="toast-body bg-white">
                <div class="d-flex">
                    <span class="fas fa-lightbulb text-warning fs-4 me-3 mt-1 flex-shrink-0"></span>
                    <div>
                        <h6 class="text-uppercase text-primary fw-bold fs--2 mb-1" style="letter-spacing:.05em;">Le conseil du jour</h6>
                        <p class="mb-0 text-700 fs--1 lh-base">{{ $conseil }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Affiche le toast de bienvenue dès le chargement, puis auto-masquage (délai Bootstrap).
            document.addEventListener('DOMContentLoaded', function () {
                var el = document.getElementById('toastBienvenue');
                if (el && window.bootstrap && bootstrap.Toast) {
                    bootstrap.Toast.getOrCreateInstance(el).show();
                }
            });
        </script>
    @endpush
@endif
