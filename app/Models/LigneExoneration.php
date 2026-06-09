<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneExoneration extends Model
{
    protected $table = 'ligne_exoneration';
    public $timestamps = false;
    const UPDATED_AT = null;
    protected $guarded = ['id'];

    protected $casts = [
        'taux'       => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function exoneration(): BelongsTo
    {
        return $this->belongsTo(Exoneration::class);
    }

    public function natureTaxe(): BelongsTo
    {
        return $this->belongsTo(NatureTaxe::class);
    }
}
