<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\DossierFiltreForm;
use App\Models\CategorieEtatDossier;
use App\Models\Dossier;
use App\Models\FamilleEtatDossier;
use App\Services\ExcelExportService;
use App\Services\SelectOptionsService;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class DossierController extends Controller implements HasMiddleware
{
    /**
     * Autorisation par action (spatie). Réf. catalogue : RolePermissionSeeder.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:DOSSIER_CONSULTER', only: ['index', 'export']),
        ];
    }

    private const COLONNES_TRI = [
        'numero', 'etablissement_id', 'famille_etat_dossier_id',
        'categorie_etat_dossier_id', 'date_creation', 'archive', 'created_at',
    ];

    public function __construct(private SelectOptionsService $selectOptions) {}

    public function index(Request $request)
    {
        $famillesEtat    = $this->selectOptions->charger(FamilleEtatDossier::class, 'libelle');
        $categoriesEtat  = $this->selectOptions->charger(CategorieEtatDossier::class, 'libelle');

        $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                       ? $request->query('sort') : 'created_at';
        $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        $filtre = DossierFiltreForm::fromRequest($request);

        $dossiers = $filtre->appliquer(
            Dossier::with([
                'etablissement.contribuable',
                'familleEtatDossier',
                'categorieEtatDossier',
            ])
        )->orderBy($sortActuel, $dirActuelle)->paginate(15)->withQueryString();

        return view('dossiers.index', compact(
            'dossiers', 'filtre', 'famillesEtat', 'categoriesEtat', 'sortActuel', 'dirActuelle',
        ));
    }

    public function export(Request $request, ExcelExportService $excel): BinaryFileResponse
    {
        $filtre = DossierFiltreForm::fromRequest($request);

        return $excel->telecharger('dossiers', function (Writer $writer) use ($filtre): void {
            $entete = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'N° Dossier', 'Établissement', 'Contribuable', 'N° Identifiant',
                'Famille état', 'Catégorie état', 'Date création', 'Date sortie', 'Archivé',
            ], $entete));

            $filtre->appliquer(
                Dossier::with(['etablissement.contribuable', 'familleEtatDossier', 'categorieEtatDossier'])
            )->orderBy('created_at', 'desc')->chunk(500, function ($liste) use ($writer): void {
                foreach ($liste as $d) {
                    $contrib = $d->etablissement?->contribuable;
                    $nom = $contrib
                        ? ($contrib->type_personne === 'PP'
                            ? trim(($contrib->nom ?? '') . ' ' . ($contrib->prenoms ?? ''))
                            : ($contrib->raison_sociale ?? ''))
                        : '';

                    $writer->addRow(Row::fromValues([
                        $d->numero ?? '',
                        $d->etablissement?->denomination ?? $d->etablissement?->numero ?? '',
                        $nom,
                        $contrib?->numero_identifiant ?? '',
                        $d->familleEtatDossier?->libelle ?? '',
                        $d->categorieEtatDossier?->libelle ?? '',
                        $d->date_creation?->format('d/m/Y') ?? '',
                        $d->date_sortie?->format('d/m/Y') ?? '',
                        $d->archive ? 'Oui' : 'Non',
                    ]));
                }
            });
        });
    }
}
