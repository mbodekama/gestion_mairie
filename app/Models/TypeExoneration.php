<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeExoneration extends Model
{
    protected $table = 'type_exoneration';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function exonerations(): HasMany
    {
        return $this->hasMany(Exoneration::class);
    }
}
