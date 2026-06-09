<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    protected $table = 'organisation';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function typeCollectivite(): BelongsTo
    {
        return $this->belongsTo(TypeCollectivite::class);
    }

    public function dirigeants(): HasMany
    {
        return $this->hasMany(Dirigeant::class);
    }
}
