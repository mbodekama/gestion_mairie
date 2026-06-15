<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransitionControle extends Model
{
    protected $table = 'transition_controle';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function etatSource(): BelongsTo
    {
        return $this->belongsTo(EtatControle::class, 'etat_source_id');
    }

    public function etatCible(): BelongsTo
    {
        return $this->belongsTo(EtatControle::class, 'etat_cible_id');
    }
}
