<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exoneration extends Model
{
    protected $table = 'exoneration';
    public $timestamps = false;
    const UPDATED_AT = null;
    protected $guarded = ['id'];

    protected $casts = [
        'date_decret' => 'date',
        'date_debut'  => 'date',
        'date_fin'    => 'date',
        'created_at'  => 'datetime',
    ];

    public function contribuable(): BelongsTo
    {
        return $this->belongsTo(Contribuable::class);
    }

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function typeExoneration(): BelongsTo
    {
        return $this->belongsTo(TypeExoneration::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneExoneration::class);
    }
}
