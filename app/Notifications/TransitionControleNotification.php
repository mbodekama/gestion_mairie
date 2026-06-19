<?php

namespace App\Notifications;

use App\Models\ControleFiscal;
use App\Models\EtatControle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notifie les agents concernés qu'un contrôle fiscal a franchi une transition
 * de workflow (validation, exécution, clôture, redressement…). Mise en file
 * (Redis) afin de ne pas ralentir la requête de transition.
 */
class TransitionControleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private ControleFiscal $controle,
        private EtatControle $etatCible,
        private string $acteur,
    ) {
    }

    /** Canal in-app uniquement : application interne, pas d'e-mail ici. */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** Charge utile stockée en base et lue par la cloche du bandeau. */
    public function toArray(object $notifiable): array
    {
        return [
            'controle_id'  => $this->controle->id,
            'numero'       => $this->controle->numero,
            'etat_code'    => $this->etatCible->code,
            'etat_libelle' => $this->etatCible->libelle,
            'message'      => "Contrôle {$this->controle->numero} : passage à « {$this->etatCible->libelle} » par {$this->acteur}.",
            'url'          => route('controles.show', $this->controle->id),
            'icone'        => 'fa-clipboard-check',
        ];
    }
}
