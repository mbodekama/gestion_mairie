<?php

namespace App\Http\Controllers;

use App\Http\FiltreDataForm\CampagneMailFiltreForm;
use App\Http\FiltreDataForm\ContribuableFiltreForm;
use App\Http\Requests\MailGroupeRequest;
use App\Jobs\EnvoyerCampagneMail;
use App\Models\CampagneMail;
use App\Models\Collectivite;
use App\Models\RegimeImposition;
use App\Models\StatutContribuable;
use App\Models\TypePersonne;
use App\Services\MailGroupeService;
use App\Services\SelectOptionsService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Campagnes de mails groupés aux contribuables (section Contribuable).
 *
 * - index   : retrace les campagnes planifiées / envoyées.
 * - create  : ciblage (filtre contribuable) + composition + date d'envoi prévue.
 * - store   : enregistre la campagne et programme l'envoi via un job différé.
 */
class MailGroupeController extends Controller
{
    public function __construct(
        private SelectOptionsService $selectOptions,
        private MailGroupeService $mailGroupe,
    ) {}

    public function index(Request $request): View
    {
        $filtre = CampagneMailFiltreForm::fromRequest($request);

        $campagnes = $filtre->appliquer(CampagneMail::query())
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('contribuables.mails-groupes.index', compact('campagnes', 'filtre'));
    }

    public function create(Request $request): View
    {
        $regimes       = $this->selectOptions->charger(RegimeImposition::class, 'libelle_court');
        $typesPersonne = $this->selectOptions->charger(TypePersonne::class, 'code');
        $statuts       = $this->selectOptions->charger(StatutContribuable::class, 'code');

        $filtre = ContribuableFiltreForm::fromRequest($request);

        $destinataires = $this->mailGroupe->destinatairesQuery($filtre)
            ->orderBy('updated_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('contribuables.mails-groupes.create', compact(
            'destinataires', 'filtre', 'regimes', 'typesPersonne', 'statuts',
        ));
    }

    public function store(MailGroupeRequest $request): RedirectResponse
    {
        $filtre = ContribuableFiltreForm::fromRequest($request);

        $nombreCibles = $this->mailGroupe->destinatairesQuery($filtre)->count();

        if ($nombreCibles === 0) {
            return back()->withInput()->with(
                'error',
                'Aucun contribuable disposant d\'une adresse e-mail ne correspond à ces critères : campagne non créée.',
            );
        }

        // Date prévue : jamais dans le passé (un envoi « immédiat » part dès que le worker prend le job).
        $datePrevue = Carbon::parse($request->validated('date_envoi_prevue'));
        if ($datePrevue->isPast()) {
            $datePrevue = now();
        }

        $campagne = CampagneMail::create([
            'numero'            => $this->genererNumero(),
            'collectivite_id'   => Collectivite::value('id'),
            'objet'             => $request->validated('objet'),
            'message'           => $request->validated('message'),
            'criteres'          => $filtre->toArray(),
            'nombre_cibles'     => $nombreCibles,
            'statut'            => CampagneMail::STATUT_EN_ATTENTE,
            'date_envoi_prevue' => $datePrevue,
            'created_by'        => auth()->id(),
        ]);

        // Le job ne s'exécutera qu'à l'échéance grâce au délai.
        EnvoyerCampagneMail::dispatch($campagne)->delay($datePrevue);

        $quand = $datePrevue->isToday()
            ? 'pour un envoi imminent'
            : 'pour le ' . $datePrevue->format('d/m/Y à H:i');

        return redirect()->route('contribuables.mails-groupes.index')
            ->with('success', "Campagne {$campagne->numero} planifiée {$quand} ({$nombreCibles} destinataire(s)).");
    }

    /** Génère un N° d'ordre séquentiel par année : CMP-AAAA-000001. */
    private function genererNumero(): string
    {
        $annee   = now()->year;
        $prefixe = "CMP-{$annee}-";
        $dernier = CampagneMail::where('numero', 'like', "{$prefixe}%")->max('numero');
        $sequence = $dernier ? ((int) substr($dernier, strlen($prefixe))) + 1 : 1;

        return $prefixe . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }
}
