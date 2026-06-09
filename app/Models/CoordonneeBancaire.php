<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoordonneeBancaire extends Model
{
    protected $table = 'coordonnee_bancaire';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function contribuable(): BelongsTo
    {
        return $this->belongsTo(Contribuable::class);
    }

    public function banque(): BelongsTo
    {
        return $this->belongsTo(Banque::class);
    }
}
