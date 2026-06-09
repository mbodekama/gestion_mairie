<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueDossier extends Model
{
    protected $table = 'historique_dossier';
    public $timestamps = false;
    const UPDATED_AT = null;
    protected $guarded = ['id'];

    protected $casts = [
        'date_mouvement' => 'date',
        'archive'        => 'boolean',
        'created_at'     => 'datetime',
    ];

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function serviceOrigine(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_origine_id');
    }

    public function serviceDestination(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_destination_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
