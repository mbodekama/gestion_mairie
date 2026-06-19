<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    protected $table = 'agent';
    protected $guarded = ['id'];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function fonctionAgent(): BelongsTo
    {
        return $this->belongsTo(FonctionAgent::class);
    }

    public function gradeAgent(): BelongsTo
    {
        return $this->belongsTo(GradeAgent::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function superieur(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'superieur_id');
    }

    public function subordonnes(): HasMany
    {
        return $this->hasMany(Agent::class, 'superieur_id');
    }

    public function utilisateurs(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class, 'agent_retrait_id');
    }

    public function convocations(): HasMany
    {
        return $this->hasMany(Convocation::class);
    }
}
