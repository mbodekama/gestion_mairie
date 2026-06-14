<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocType extends Model
{
    protected $table   = 'doc_type';
    protected $guarded = ['id'];

    protected $casts = [
        'obligatoire' => 'boolean',
        'ordre'       => 'integer',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /** Extensions autorisées sous forme de tableau. */
    public function extensionsAutorisees(): array
    {
        if (!$this->extensions_autorisees) {
            return [];
        }
        return array_map('trim', explode(',', strtolower($this->extensions_autorisees)));
    }

    /** Retourne les types applicables à un modèle donné. */
    public static function pourModele(string $modelClass)
    {
        return static::where('model_type', $modelClass)
            ->orderBy('ordre')
            ->orderBy('libelle')
            ->get();
    }
}
