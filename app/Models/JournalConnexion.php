<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalConnexion extends Model
{
    protected $table = 'journal_connexion';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'succes'     => 'boolean',
        'horodatage' => 'datetime',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
