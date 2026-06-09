<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $table = 'district';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function collectivites(): HasMany
    {
        return $this->hasMany(Collectivite::class);
    }
}
