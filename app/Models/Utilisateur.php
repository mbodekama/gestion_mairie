<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class Utilisateur extends Authenticatable
{
    protected $table = 'utilisateur';
    protected $guarded = ['id'];

    protected $hidden = ['mot_de_passe'];

    protected $casts = [
        'mfa_active'         => 'boolean',
        'date_creation'      => 'date',
        'date_expiration'    => 'date',
        'derniere_connexion' => 'datetime',
    ];

    // Spatie utilise 'password' par convention ; on lui indique la colonne réelle
    public function getAuthPassword(): string
    {
        return $this->mot_de_passe;
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function journalConnexions(): HasMany
    {
        return $this->hasMany(JournalConnexion::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
