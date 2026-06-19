<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Campagne de mails groupés adressée aux contribuables.
 * Le cycle de vie est porté par le statut, mis à jour par le job d'envoi.
 */
class CampagneMail extends Model
{
    protected $table = 'campagne_mail';
    protected $guarded = ['id'];

    public const STATUT_EN_ATTENTE = 'EN_ATTENTE';
    public const STATUT_EN_COURS   = 'EN_COURS';
    public const STATUT_ENVOYE     = 'ENVOYE';
    public const STATUT_ECHEC      = 'ECHEC';

    protected $casts = [
        'criteres'             => 'array',
        'nombre_cibles'        => 'integer',
        'nombre_envoyes'       => 'integer',
        'date_envoi_prevue'    => 'datetime',
        'date_envoi_effective' => 'datetime',
    ];

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    /** Libellé lisible du statut. */
    public function statutLibelle(): string
    {
        return match ($this->statut) {
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_EN_COURS   => 'En cours',
            self::STATUT_ENVOYE     => 'Envoyé',
            self::STATUT_ECHEC      => 'Échec',
            default                 => $this->statut,
        };
    }

    /** Classe Bootstrap du badge de statut. */
    public function statutClasse(): string
    {
        return match ($this->statut) {
            self::STATUT_EN_ATTENTE => 'warning',
            self::STATUT_EN_COURS   => 'info',
            self::STATUT_ENVOYE     => 'success',
            self::STATUT_ECHEC      => 'danger',
            default                 => 'secondary',
        };
    }
}
