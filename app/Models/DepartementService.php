<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DepartementService extends Model
{
    protected $table = 'departement_service';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function typeCollectivite(): BelongsTo
    {
        return $this->belongsTo(TypeCollectivite::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
