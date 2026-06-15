<?php

namespace App\Models;

use App\Traits\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Convocation extends Model
{
    use HasDocuments;

    protected $table = 'convocation';
    public $timestamps = false;
    const UPDATED_AT = null;
    protected $guarded = ['id'];

    protected $casts = [
        'date_convocation'   => 'date',
        'date_limite'        => 'date',
        'date_reponse'       => 'date',
        'periode_due_debut'  => 'date',
        'periode_due_fin'    => 'date',
        'montant_du'         => 'decimal:2',
        'created_at'         => 'datetime',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /** Contrôle fiscal à l'origine de cette convocation (le cas échéant). */
    public function controle(): BelongsTo
    {
        return $this->belongsTo(ControleFiscal::class, 'controle_id');
    }
}
