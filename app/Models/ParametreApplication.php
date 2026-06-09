<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParametreApplication extends Model
{
    protected $table = 'parametre_application';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }
}
