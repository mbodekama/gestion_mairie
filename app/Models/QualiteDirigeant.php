<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QualiteDirigeant extends Model
{
    protected $table = 'qualite_dirigeant';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function dirigeants(): HasMany
    {
        return $this->hasMany(Dirigeant::class);
    }
}
