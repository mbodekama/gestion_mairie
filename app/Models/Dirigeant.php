<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dirigeant extends Model
{
    protected $table = 'dirigeant';
    protected $guarded = ['id'];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
        'actif'      => 'boolean',
    ];

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function qualiteDirigeant(): BelongsTo
    {
        return $this->belongsTo(QualiteDirigeant::class);
    }
}
