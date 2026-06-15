<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne du rapport de contrôle : écart constaté pour une nature de taxe
 * (et éventuellement un exercice), avec la sanction applicable.
 */
class ControleConstat extends Model
{
    protected $table = 'controle_constat';
    public $timestamps = false;
    const UPDATED_AT = null;
    protected $guarded = ['id'];

    protected $casts = [
        'montant_declare' => 'decimal:2',
        'montant_verifie' => 'decimal:2',
        'ecart'           => 'decimal:2',
        'created_at'      => 'datetime',
    ];

    public function controleFiscal(): BelongsTo
    {
        return $this->belongsTo(ControleFiscal::class);
    }

    public function natureTaxe(): BelongsTo
    {
        return $this->belongsTo(NatureTaxe::class);
    }

    public function exerciceFiscal(): BelongsTo
    {
        return $this->belongsTo(ExerciceFiscal::class);
    }

    public function sanctionFiscale(): BelongsTo
    {
        return $this->belongsTo(SanctionFiscale::class);
    }
}
