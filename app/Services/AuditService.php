<?php

namespace App\Services;

use App\Models\AuditLog;

/**
 * Journalisation des actions sensibles dans `audit_log`.
 *
 * `action` est contraint à INSERT / UPDATE / DELETE (contrainte CHECK en base).
 * Une description lisible peut être portée par la clé `_evenement` de
 * `donnees_apres` (cf. AuditLog::descriptionLisible()).
 */
class AuditService
{
    public function enregistrer(
        string $tableCible,
        int|string $cleLigne,
        string $action,
        ?array $avant = null,
        ?array $apres = null,
    ): void {
        AuditLog::create([
            'table_cible'    => $tableCible,
            'cle_ligne'      => (string) $cleLigne,
            'action'         => $action,
            'donnees_avant'  => $avant,
            'donnees_apres'  => $apres,
            'utilisateur_id' => auth()->id(),
        ]);
    }
}
