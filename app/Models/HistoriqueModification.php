<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueModification extends Model
{
    protected $table    = 'historique_modification';
    public $timestamps  = false;
    protected $guarded  = ['id'];

    protected $casts = [
        'donnees_avant' => 'array',
        'donnees_apres' => 'array',
        'created_at'    => 'datetime',
    ];

    public const CREATION     = 'CREATION';
    public const MODIFICATION = 'MODIFICATION';
    public const SUPPRESSION  = 'SUPPRESSION';

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    /**
     * Persiste une entrée d'historique depuis un observer ou un trait.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $modele
     * @param  string  $evenement
     * @param  array<string,mixed>  $avant
     * @param  array<string,mixed>  $apres
     */
    public static function enregistrer(Model $modele, string $evenement, array $avant, array $apres): void
    {
        $user = auth()->user();

        static::create([
            'model_type'      => $modele::class,
            'model_id'        => $modele->getKey(),
            'evenement'       => $evenement,
            'utilisateur_id'  => $user?->id,
            'utilisateur_nom' => $user?->name,
            'donnees_avant'   => $avant ?: null,
            'donnees_apres'   => $apres ?: null,
        ]);
    }
}
