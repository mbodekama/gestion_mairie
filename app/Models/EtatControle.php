<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EtatControle extends Model
{
    protected $table = 'etat_controle';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'ordre'     => 'integer',
        'est_final' => 'boolean',
    ];

    /** Transitions partant de cet état. */
    public function transitionsSortantes(): HasMany
    {
        return $this->hasMany(TransitionControle::class, 'etat_source_id');
    }

    /** Transitions arrivant à cet état. */
    public function transitionsEntrantes(): HasMany
    {
        return $this->hasMany(TransitionControle::class, 'etat_cible_id');
    }
}
