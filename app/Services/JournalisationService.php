<?php

namespace App\Services;

use App\Models\JournalConnexion;
use Illuminate\Http\Request;

class JournalisationService
{
    public const CONNEXION      = 'CONNEXION';
    public const DECONNEXION    = 'DECONNEXION';
    public const VERROUILLAGE   = 'VERROUILLAGE';
    public const DEVERROUILLAGE = 'DEVERROUILLAGE';

    public function enregistrer(string $typeEvenement, Request $request, bool $succes = true): void
    {
        $this->enregistrerPourEmail(
            $typeEvenement,
            auth()->user()?->email,
            $request,
            $succes
        );
    }

    public function enregistrerPourEmail(
        string $typeEvenement,
        ?string $email,
        Request $request,
        bool $succes = true
    ): void {
        JournalConnexion::create([
            'login'          => mb_substr($email ?? '', 0, 64) ?: null,
            'application'    => config('app.name'),
            'type_evenement' => $typeEvenement,
            'succes'         => $succes,
            'adresse_ip'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
            'horodatage'     => now(),
        ]);
    }
}
