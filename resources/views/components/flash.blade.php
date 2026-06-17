{{--
    Messages flash de session, style Falcon « Alerts with icon ».

    Rendu automatiquement une fois dans le layout (avant le contenu de page) :
    affiche les clés de session success / info / warning / error.

    Usage côté contrôleur :
        return redirect()->route(...)->with('success', 'Élément enregistré.');
        ->with('error', '…')  ->with('warning', '…')  ->with('info', '…')
--}}
@php
    $flashes = [
        'success' => ['cls' => 'success', 'bg' => 'bg-success', 'icon' => 'fa-check-circle'],
        'info'    => ['cls' => 'info',    'bg' => 'bg-info',    'icon' => 'fa-info-circle'],
        'warning' => ['cls' => 'warning', 'bg' => 'bg-warning', 'icon' => 'fa-exclamation-circle'],
        'error'   => ['cls' => 'danger',  'bg' => 'bg-danger',  'icon' => 'fa-times-circle'],
    ];
@endphp

@foreach ($flashes as $cle => $cfg)
    @if (session($cle))
        <div class="alert alert-{{ $cfg['cls'] }} border-0 d-flex align-items-center alert-dismissible fade show js-flash"
             role="alert" data-flash-cle="{{ $cle }}">
            <div class="{{ $cfg['bg'] }} me-3 icon-item">
                <span class="fas {{ $cfg['icon'] }} text-white fs-6"></span>
            </div>
            <p class="mb-0 flex-1">{{ session($cle) }}</p>
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif
@endforeach

@once
    @push('scripts')
        <script>
            // Auto-fermeture des messages de succès / info après quelques secondes.
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.js-flash[data-flash-cle="success"], .js-flash[data-flash-cle="info"]')
                    .forEach(function (el) {
                        setTimeout(function () {
                            if (window.bootstrap && bootstrap.Alert) {
                                bootstrap.Alert.getOrCreateInstance(el).close();
                            } else {
                                el.remove();
                            }
                        }, 6000);
                    });
            });
        </script>
    @endpush
@endonce
