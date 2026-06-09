<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nationalite extends Model
{
    protected $table = 'nationalite';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }

    public function contribuables(): HasMany
    {
        return $this->hasMany(Contribuable::class);
    }
}
