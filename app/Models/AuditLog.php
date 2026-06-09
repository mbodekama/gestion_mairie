<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_log';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'donnees_avant' => 'array',
        'donnees_apres' => 'array',
        'horodatage'    => 'datetime',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
