<?php

namespace App\Jobs;

use App\Models\CampagneMail;
use App\Services\MailGroupeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Traite une campagne de mails groupés à sa date prévue.
 *
 * Le job est dispatché avec un délai (->delay) correspondant à la date prévue :
 * il n'est exécuté par le worker qu'une fois l'échéance atteinte. Il re-résout
 * les destinataires depuis les critères mémorisés, met les courriels en file
 * (un par contribuable) et fait progresser le statut de la campagne.
 */
class EnvoyerCampagneMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public CampagneMail $campagne) {}

    public function handle(MailGroupeService $mailGroupe): void
    {
        $campagne = $this->campagne->fresh();

        // Idempotence : ne traiter qu'une campagne encore en attente.
        if (! $campagne || $campagne->statut !== CampagneMail::STATUT_EN_ATTENTE) {
            return;
        }

        $campagne->update(['statut' => CampagneMail::STATUT_EN_COURS]);

        $nombre = $mailGroupe->envoyer($campagne);

        $campagne->update([
            'statut'               => CampagneMail::STATUT_ENVOYE,
            'nombre_envoyes'       => $nombre,
            'date_envoi_effective' => now(),
        ]);
    }

    public function failed(?Throwable $e): void
    {
        $this->campagne->fresh()?->update(['statut' => CampagneMail::STATUT_ECHEC]);
    }
}
