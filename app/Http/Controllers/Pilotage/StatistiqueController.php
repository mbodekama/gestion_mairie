<?php

namespace App\Http\Controllers\Pilotage;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatistiqueCalibreeRequest;
use App\Services\StatistiqueCalibreeService;
use App\Services\TableauBordService;
use Carbon\Carbon;
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

    /**
     * Statistique calibrée : affiche le formulaire de paramétrage (sans résultat).
     */
    public function calibree(): View
    {
        $collectiviteId = DB::table('collectivite')->where('active', true)->value('id');

        return view('pilotage.statistiques.calibree', [
            'collectiviteAbsente' => $collectiviteId === null,
            'objets'              => StatistiqueCalibreeService::OBJETS,
            'granularites'        => StatistiqueCalibreeService::GRANULARITES,
            'diagrammes'          => StatistiqueCalibreeService::DIAGRAMMES,
            'resultat'            => null,
            'dateDebut'           => null,
            'dateFin'             => null,
        ]);
    }

    /**
     * Génère la statistique calibrée à partir des critères saisis et renvoie le
     * formulaire repeuplé accompagné du graphique demandé.
     */
    public function calibreeGenerer(
        StatistiqueCalibreeRequest $request,
        StatistiqueCalibreeService $service,
    ): View {
        $collectiviteId = DB::table('collectivite')->where('active', true)->value('id');

        if ($collectiviteId === null) {
            return view('pilotage.statistiques.calibree', [
                'collectiviteAbsente' => true,
                'objets'              => StatistiqueCalibreeService::OBJETS,
                'granularites'        => StatistiqueCalibreeService::GRANULARITES,
                'diagrammes'          => StatistiqueCalibreeService::DIAGRAMMES,
                'resultat'            => null,
                'dateDebut'           => null,
                'dateFin'             => null,
            ]);
        }

        $valide = $request->validated();

        // Conversion des dates Flatpickr (d/m/Y) en Y-m-d pour la requête.
        $dateDebut = filled($valide['date_debut'] ?? null)
            ? Carbon::createFromFormat('d/m/Y', $valide['date_debut'])->toDateString() : null;
        $dateFin = filled($valide['date_fin'] ?? null)
            ? Carbon::createFromFormat('d/m/Y', $valide['date_fin'])->toDateString() : null;

        $resultat = $service->generer(
            collectiviteId: (int) $collectiviteId,
            objet:          $valide['objet'],
            granularite:    $valide['granularite'],
            diagramme:      $valide['type_diagramme'],
            dateDebut:      $dateDebut,
            dateFin:        $dateFin,
            objetCompare:   $valide['objet_compare'] ?? null,
        );

        return view('pilotage.statistiques.calibree', [
            'collectiviteAbsente' => false,
            'objets'              => StatistiqueCalibreeService::OBJETS,
            'granularites'        => StatistiqueCalibreeService::GRANULARITES,
            'diagrammes'          => StatistiqueCalibreeService::DIAGRAMMES,
            'resultat'            => $resultat,
            'dateDebut'           => $dateDebut,
            'dateFin'             => $dateFin,
        ]);
    }
}
