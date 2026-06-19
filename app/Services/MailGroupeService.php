<?php

namespace App\Services;

use App\Http\FiltreDataForm\ContribuableFiltreForm;
use App\Mail\MailGroupeContribuable;
use App\Models\CampagneMail;
use App\Models\Contribuable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

/**
 * Envoi groupé d'e-mails aux contribuables.
 *
 * Le périmètre des destinataires est déterminé par le même filtre que la liste
 * des contribuables (ContribuableFiltreForm), restreint à ceux disposant d'une
 * adresse e-mail renseignée. L'envoi est mis en file d'attente : un job de
 * courriel par destinataire (voir MailGroupeContribuable).
 */
class MailGroupeService
{
    /**
     * Requête des contribuables ciblés : non supprimés, possédant un e-mail,
     * et correspondant aux critères du filtre.
     */
    public function destinatairesQuery(ContribuableFiltreForm $filtre): Builder
    {
        return $filtre->appliquer(
            Contribuable::query()
                ->whereNull('supprime_le')
                ->whereNotNull('email')
                ->where('email', '!=', '')
        );
    }

    /**
     * Met en file les courriels de la campagne pour tous les destinataires
     * ciblés et retourne le nombre de courriels programmés.
     */
    public function envoyer(CampagneMail $campagne): int
    {
        $filtre = ContribuableFiltreForm::fromArray($campagne->criteres ?? []);

        $nombre = 0;

        $this->destinatairesQuery($filtre)
            ->orderBy('id')
            ->chunkById(200, function ($lot) use (&$nombre, $campagne): void {
                foreach ($lot as $contribuable) {
                    Mail::to($contribuable->email)->queue(
                        new MailGroupeContribuable($contribuable, $campagne->objet, $campagne->message)
                    );
                    $nombre++;
                }
            });

        return $nombre;
    }
}
