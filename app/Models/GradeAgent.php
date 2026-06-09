<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeAgent extends Model
{
    protected $table = 'grade_agent';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }
}
