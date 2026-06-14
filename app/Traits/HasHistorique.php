<?php

namespace App\Traits;

use App\Models\HistoriqueModification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Journalise automatiquement les créations, modifications et suppressions.
 *
 * Usage :  use HasHistorique;
 *
 * Options sur le modèle cible :
 *   protected array $auditExclu  = ['champ_a_ignorer'];
 *   protected array $auditLabels = ['nom' => 'Nom complet'];  // labels lisibles
 *   protected array $auditMasque = ['mot_de_passe'];         // affiche ***
 */
trait HasHistorique
{
    // Champs toujours exclus de la diff, quelle que soit la configuration du modèle.
    private static array $CHAMPS_SYSTEME = [
        'updated_at', 'created_at', 'supprime_le',
        'remember_token', 'two_factor_secret',
    ];

    public static function bootHasHistorique(): void
    {
        static::created(function (Model $model) {
            $apres = self::filtrer($model, $model->getAttributes());
            HistoriqueModification::enregistrer($model, HistoriqueModification::CREATION, [], $apres);
        });

        static::updated(function (Model $model) {
            $dirty  = $model->getDirty();
            $avant  = array_intersect_key($model->getOriginal(), $dirty);
            $apres  = $dirty;

            $avant  = self::filtrer($model, $avant);
            $apres  = self::filtrer($model, $apres);

            if (empty($apres)) {
                return;
            }

            HistoriqueModification::enregistrer($model, HistoriqueModification::MODIFICATION, $avant, $apres);
        });

        static::deleted(function (Model $model) {
            $avant = self::filtrer($model, $model->getAttributes());
            HistoriqueModification::enregistrer($model, HistoriqueModification::SUPPRESSION, $avant, []);
        });
    }

    public function historique(): HasMany
    {
        return $this->hasMany(HistoriqueModification::class, 'model_id')
            ->where('model_type', static::class)
            ->latest('created_at');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────

    private static function filtrer(Model $model, array $donnees): array
    {
        $exclus = array_merge(
            self::$CHAMPS_SYSTEME,
            $model->auditExclu ?? []
        );

        $masques = $model->auditMasque ?? [];

        $filtrees = array_diff_key($donnees, array_flip($exclus));

        foreach ($masques as $champ) {
            if (array_key_exists($champ, $filtrees)) {
                $filtrees[$champ] = '***';
            }
        }

        return $filtrees;
    }
}
