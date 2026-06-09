<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $table = 'service';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function departementService(): BelongsTo
    {
        return $this->belongsTo(DepartementService::class);
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function convocations(): HasMany
    {
        return $this->hasMany(Convocation::class);
    }
}
