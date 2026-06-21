<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_log';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'donnees_avant' => 'array',
        'donnees_apres' => 'array',
        'horodatage'    => 'datetime',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    /**
     * Libellé lisible de l'événement : priorité à la clé `_evenement` portée
     * par `donnees_apres`, sinon repli sur (table cible × action).
     */
    public function descriptionLisible(): string
    {
        if (! empty($this->donnees_apres['_evenement'])) {
            return $this->donnees_apres['_evenement'];
        }

        return match ("{$this->table_cible}:{$this->action}") {
            'users:INSERT' => 'Création du compte utilisateur',
            'users:UPDATE' => 'Modification du compte utilisateur',
            'users:DELETE' => 'Suppression du compte utilisateur',
            'agent:UPDATE' => 'Modification de la fiche agent',
            default        => "{$this->action} sur {$this->table_cible}",
        };
    }
}
