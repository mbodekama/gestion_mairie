<?php

namespace App\Traits;

use App\Models\DocType;
use App\Models\Document;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Attache la gestion de pièces jointes à n'importe quel modèle.
 *
 * Usage : use HasDocuments;
 *
 * Dans les contrôleurs, charger les types via :
 *   $typesDocuments = DocType::pourModele(Contribuable::class);
 */
trait HasDocuments
{
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'model', 'model_type', 'model_id')
            ->latest('created_at');
    }
}
