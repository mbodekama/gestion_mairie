<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Journal des transitions d'état d'un contrôle fiscal.
 */
class HistoriqueControle extends Model
{
    protected $table = 'historique_controle';
    public $timestamps = false;
    const UPDATED_AT = null;
    protected $guarded = ['id'];

    protected $casts = [
        'date_mouvement' => 'date',
        'created_at'     => 'datetime',
    ];

    public function controleFiscal(): BelongsTo
    {
        return $this->belongsTo(ControleFiscal::class);
    }

    public function etatSource(): BelongsTo
    {
        return $this->belongsTo(EtatControle::class, 'etat_source_id');
    }

    public function etatCible(): BelongsTo
    {
        return $this->belongsTo(EtatControle::class, 'etat_cible_id');
    }
}
