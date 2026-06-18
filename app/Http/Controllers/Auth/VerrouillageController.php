<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DeverrouillageRequest;
use App\Services\JournalisationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerrouillageController extends Controller
{
    public function __construct(private JournalisationService $journalisation) {}

    public function show(): View
    {
        return view('auth.verrouillage');
    }

    public function verrouiller(Request $request): RedirectResponse
    {
        // Poser le verrou d'abord — la journalisation ne doit pas bloquer le verrouillage
        session(['verrouille' => true]);

        try {
            $this->journalisation->enregistrer(JournalisationService::VERROUILLAGE, $request);
        } catch (\Throwable) {
        }

        return redirect()->route('verrouillage.show');
    }

    public function deverrouiller(DeverrouillageRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Retirer le verrou avant de journaliser — l'échec DB ne bloque pas le déverrouillage
        session()->forget('verrouille');

        try {
            $this->journalisation->enregistrer(JournalisationService::DEVERROUILLAGE, $request);
        } catch (\Throwable) {
        }

        return redirect()->route('dashboard')
            ->with('toast_bienvenue', __('Bon retour, :name !', ['name' => $request->user()->name]));
    }
}
