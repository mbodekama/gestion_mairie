<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $table    = 'document';
    public $timestamps  = false;
    protected $guarded  = ['id'];

    protected $casts = [
        'taille'     => 'integer',
        'created_at' => 'datetime',
    ];

    public function docType(): BelongsTo
    {
        return $this->belongsTo(DocType::class);
    }

    public function uploadeur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /** Taille formatée lisible (Ko, Mo). */
    public function tailleListible(): string
    {
        $o = (int) $this->taille;
        if ($o < 1024)    return $o . ' o';
        if ($o < 1048576) return round($o / 1024, 1) . ' Ko';
        return round($o / 1048576, 1) . ' Mo';
    }

    /** Icône Font Awesome selon le type MIME. */
    public function icone(): string
    {
        return match (true) {
            str_contains($this->mime_type ?? '', 'pdf')   => 'fas fa-file-pdf text-danger',
            str_contains($this->mime_type ?? '', 'image') => 'fas fa-file-image text-info',
            str_contains($this->mime_type ?? '', 'word')  => 'fas fa-file-word text-primary',
            str_contains($this->mime_type ?? '', 'sheet') ||
            str_contains($this->mime_type ?? '', 'excel') => 'fas fa-file-excel text-success',
            default                                        => 'fas fa-file-alt text-secondary',
        };
    }

    public function existeSurDisque(): bool
    {
        return Storage::disk('local')->exists($this->chemin);
    }
}
