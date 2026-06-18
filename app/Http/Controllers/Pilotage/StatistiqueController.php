<?php

namespace App\Http\Controllers\Pilotage;

use App\Http\Controllers\Controller;
use App\Services\TableauBordService;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Statistiques de la mairie : synthèse analytique de l'activité fiscale de la
 * collectivité (indicateurs clés, évolution émissions / recouvrements, répartitions).
 *
 * Les agrégats proviennent de {@see TableauBordService} (mêmes calculs que le
 * tableau de bord, sommes monétaires réalisées côté PostgreSQL sur NUMERIC).
 */
class StatistiqueController extends Controller
{
    public function __construct(private TableauBordService $tableauBord) {}

    public function index(): View
    {
        // Mono-mairie aujourd'hui : on cible la collectivité active. Lorsqu'un lien
        // utilisateur ↔ collectivité existera, le remplacer par celui de l'agent connecté.
        $collectiviteId = DB::table('collectivite')->where('active', true)->value('id');

        if ($collectiviteId === null) {
            return view('pilotage.statistiques.index', [
                'collectiviteAbsente' => true,
                'indicateurs'         => null,
                'repartitions'        => null,
                'emissions'           => null,
                'recouvrements'       => null,
                'topContribuables'    => [],
            ]);
        }

        $collectiviteId = (int) $collectiviteId;

        return view('pilotage.statistiques.index', [
            'collectiviteAbsente' => false,
            'indicateurs'         => $this->tableauBord->indicateursCles($collectiviteId),
            'repartitions'        => $this->tableauBord->repartitions($collectiviteId),
            'emissions'           => $this->tableauBord->emissionsDouzeDerniersMois($collectiviteId),
            'recouvrements'       => $this->tableauBord->recouvrementsDouzeDerniersMois($collectiviteId),
            'topContribuables'    => $this->tableauBord->topContribuables($collectiviteId),
        ]);
    }
}
