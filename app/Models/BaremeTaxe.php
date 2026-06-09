<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaremeTaxe extends Model
{
    protected $table = 'bareme_taxe';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'ca_borne_inf' => 'decimal:2',
        'ca_borne_sup' => 'decimal:2',
        'taux'         => 'decimal:4',
    ];

    public function natureTaxe(): BelongsTo
    {
        return $this->belongsTo(NatureTaxe::class);
    }

    public function categorieActivite(): BelongsTo
    {
        return $this->belongsTo(CategorieActivite::class);
    }

    public function periodicite(): BelongsTo
    {
        return $this->belongsTo(Periodicite::class);
    }
}
