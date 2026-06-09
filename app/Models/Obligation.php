<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Obligation extends Model
{
    protected $table = 'obligation';
    public $timestamps = false;
    const UPDATED_AT = null;
    protected $guarded = ['id'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function contribuable(): BelongsTo
    {
        return $this->belongsTo(Contribuable::class);
    }

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function natureTaxe(): BelongsTo
    {
        return $this->belongsTo(NatureTaxe::class);
    }

    public function periodicite(): BelongsTo
    {
        return $this->belongsTo(Periodicite::class);
    }
}
