<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pays extends Model
{
    protected $table = 'pays';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function nationalites(): HasMany
    {
        return $this->hasMany(Nationalite::class);
    }
}
