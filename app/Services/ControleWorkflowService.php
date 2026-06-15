<?php

namespace App\Services;

use App\Models\Collectivite;
use App\Models\ControleFiscal;
use App\Models\Convocation;
use App\Models\EmissionTaxe;
use App\Models\EtatControle;
use App\Models\ExerciceFiscal;
use App\Models\HistoriqueControle;
use App\Models\Obligation;
use App\Models\Periodicite;
use App\Models\Redressement;
use App\Models\TransitionControle;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Pilote le workflow du contrôle fiscal : valide les transitions autorisées
 * (référentiel transition_controle), contrôle la permission de l'acteur,
 * applique l'effet métier (convocation, rapport, clôture, redressement) et
 * journalise chaque mouvement. Toute la logique métier du workflow vit ici.
 */
class ControleWorkflowService
{
    /** Colonne de date alimentée à l'arrivée dans un état. */
    private const DATE_PAR_ETAT = [
        'VALIDE'   => 'date_validation',
        'EXECUTE'  => 'date_execution',
        'CLOTURE'  => 'date_cloture',
    ];

    /**
     * Transitions possibles depuis l'état courant, éventuellement restreintes
     * aux permissions détenues par l'utilisateur.
     *
     * @return Collection<int, TransitionControle>
     */
    public function transitionsDisponibles(ControleFiscal $controle, ?User $user = null): Collection
    {
        $transitions = TransitionControle::with('etatCible')
            ->where('etat_source_id', $controle->etat_controle_id)
            ->get();

        if ($user) {
            $transitions = $transitions->filter(fn (TransitionControle $t) => $user->can($t->permission))->values();
        }

        return $transitions;
    }

    /**
     * Exécute une transition vers l'état cible (code). Vérifie l'existence de la
     * transition depuis l'état courant et la permission de l'acteur, applique
     * l'effet métier puis journalise le mouvement.
     *
     * @param  array<string,mixed>  $payload  Données propres à l'effet (convocation, pénalités…)
     *
     * @throws \RuntimeException             transition non autorisée depuis l'état courant
     * @throws AuthorizationException        permission manquante
     */
    public function transitionner(
        ControleFiscal $controle,
        string $codeEtatCible,
        User $user,
        array $payload = [],
        ?string $motif = null,
    ): ControleFiscal {
        $etatCible = EtatControle::where('code', $codeEtatCible)->firstOrFail();

        $transition = TransitionControle::where('etat_source_id', $controle->etat_controle_id)
            ->where('etat_cible_id', $etatCible->id)
            ->first();

        if (! $transition) {
            throw new \RuntimeException(
                "Transition non autorisée vers « {$codeEtatCible} » depuis l'état courant."
            );
        }

        if (! $user->can($transition->permission)) {
            throw new AuthorizationException(
                "Permission « {$transition->permission} » requise pour cette action."
            );
        }

        return DB::transaction(function () use ($controle, $etatCible, $transition, $user, $payload, $motif) {
            $etatSourceId = $controle->etat_controle_id;

            // Effet métier de la transition
            $this->appliquerEffet($controle, $transition->effet, $payload, $user);

            // Changement d'état + date d'étape
            $maj = [
                'etat_controle_id' => $etatCible->id,
                'updated_by'       => $user->id,
            ];
            if ($colonne = self::DATE_PAR_ETAT[$etatCible->code] ?? null) {
                $maj[$colonne] = now()->toDateString();
            }
            $controle->update($maj);

            // Journalisation du mouvement
            HistoriqueControle::create([
                'controle_fiscal_id' => $controle->id,
                'etat_source_id'     => $etatSourceId,
                'etat_cible_id'      => $etatCible->id,
                'motif'              => $motif ?? $transition->libelle_action,
                'created_by'         => $user->id,
            ]);

            return $controle->refresh();
        });
    }

    /** Aiguille vers le traitement de l'effet déclaré sur la transition. */
    private function appliquerEffet(ControleFiscal $controle, ?string $effet, array $payload, User $user): void
    {
        match ($effet) {
            'convocation'  => $this->genererConvocation($controle, $payload, $user),
            'redressement' => $this->ouvrirRedressement($controle, $payload, $user),
            // 'rapport' et 'cloture' n'ont pas de création annexe ici :
            // le rapport (constats) est saisi par l'écran, la date est posée plus haut.
            default        => null,
        };
    }

    /**
     * Crée la convocation du contribuable, livrable de la validation, et la
     * rattache au contrôle. Service et agent proviennent du payload, à défaut
     * de l'agent instructeur du contrôle.
     */
    private function genererConvocation(ControleFiscal $controle, array $payload, User $user): void
    {
        $agentId   = $payload['agent_id']   ?? $controle->agent_instructeur_id;
        $serviceId = $payload['service_id'] ?? $controle->agentInstructeur?->service_id;

        if (! $agentId || ! $serviceId) {
            throw new \RuntimeException(
                'Service et agent sont requis pour générer la convocation.'
            );
        }

        $annee = (int) ($controle->periode_fin?->year ?? now()->year);
        // numero limité à varchar(10) : CV + année sur 2 chiffres + 6 chiffres de séquence
        $prefixe = 'CV' . substr((string) $annee, -2);

        $dernier = Convocation::where('numero', 'like', "{$prefixe}%")
            ->orderBy('numero', 'desc')->value('numero');
        $seq = $dernier ? ((int) substr($dernier, -6) + 1) : 1;

        $convocation = Convocation::create([
            'numero'            => $prefixe . str_pad($seq, 6, '0', STR_PAD_LEFT),
            'etablissement_id'  => $controle->etablissement_id,
            'controle_id'       => $controle->id,
            'collectivite_id'   => $controle->collectivite_id,
            'service_id'        => $serviceId,
            'agent_id'          => $agentId,
            'annee'             => $annee,
            'motif'             => $payload['motif'] ?? $controle->motif,
            'date_convocation'  => $payload['date_convocation'] ?? now()->toDateString(),
            'delai_reponse'     => $payload['delai_reponse'] ?? null,
            'date_limite'       => $payload['date_limite'] ?? null,
            'periode_due_debut' => $controle->periode_debut?->toDateString(),
            'periode_due_fin'   => $controle->periode_fin?->toDateString(),
            'created_by'        => $user->id,
        ]);

        $controle->convocation_id = $convocation->id;
    }

    /**
     * Ouvre le dossier de redressement à partir des constats du contrôle.
     * Les droits = somme des écarts positifs ; les pénalités sont fournies en
     * payload (sinon 0). Les émissions complémentaires (déclarations
     * complémentaires) sont générées automatiquement depuis les constats.
     */
    private function ouvrirRedressement(ControleFiscal $controle, array $payload, User $user): Redressement
    {
        $constats = $controle->constats()->where('ecart', '>', 0)->get();
        $droits   = $constats->sum('ecart');

        $annee = (int) ($controle->periode_fin?->year ?? now()->year);
        $dernier = Redressement::where('numero', 'like', "REDR{$annee}%")
            ->orderBy('numero', 'desc')->value('numero');
        $seq = $dernier ? ((int) substr($dernier, -5) + 1) : 1;

        // Les pénalités sont saisies ensuite, par déclaration, dans le redressement.
        $redressement = Redressement::create([
            'numero'             => "REDR{$annee}" . str_pad($seq, 5, '0', STR_PAD_LEFT),
            'controle_fiscal_id' => $controle->id,
            'collectivite_id'    => $controle->collectivite_id,
            'montant_droits'     => $droits,
            'montant_penalites'  => 0,
            'montant_total'      => $droits,
            'etat'               => 'ouvert',
            'date_redressement'  => now()->toDateString(),
            'observation'        => $payload['observation'] ?? null,
            'created_by'         => $user->id,
        ]);

        // Déclarations complémentaires : une émission par constat redressé
        // (pénalité initiale = 0, à saisir par déclaration ensuite).
        $lignes = $this->lignesDepuisConstats($controle, $constats, $annee);
        if ($lignes !== []) {
            $this->genererEmissionsComplementaires($redressement, $lignes, $user);
        }

        return $redressement;
    }

    /**
     * Construit les lignes d'émissions complémentaires à partir des constats,
     * en résolvant l'exercice et la périodicité requis par emission_taxe :
     * exercice du constat, sinon de l'année contrôlée ; périodicité issue de
     * l'obligation du contribuable, sinon annuelle par défaut.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\ControleConstat>  $constats
     * @return array<int, array{nature_taxe_id:int, exercice_fiscal_id:int, periodicite_id:int, montant:string}>
     */
    private function lignesDepuisConstats(ControleFiscal $controle, Collection $constats, int $annee): array
    {
        $contribuableId = $controle->etablissement?->contribuable_id;

        $exerciceParAnnee = ExerciceFiscal::where('annee', $annee)->value('id')
            ?? ExerciceFiscal::where('cloture', false)->orderByDesc('annee')->value('id')
            ?? ExerciceFiscal::orderByDesc('annee')->value('id');

        // Périodicité annuelle privilégiée (nb_mois = 12), sinon la première disponible
        $periodiciteDefaut = Periodicite::orderByRaw('CASE WHEN nb_mois = 12 THEN 0 ELSE 1 END')
            ->orderBy('id')->value('id') ?? Periodicite::value('id');

        $lignes = [];
        foreach ($constats as $constat) {
            $exerciceId = $constat->exercice_fiscal_id ?? $exerciceParAnnee;

            $periodiciteId = ($contribuableId
                ? Obligation::where('contribuable_id', $contribuableId)
                    ->where('nature_taxe_id', $constat->nature_taxe_id)
                    ->value('periodicite_id')
                : null) ?? $periodiciteDefaut;

            // Sans exercice ni périodicité, l'émission ne peut être créée : ligne ignorée.
            if (! $exerciceId || ! $periodiciteId) {
                continue;
            }

            $lignes[] = [
                'nature_taxe_id'     => $constat->nature_taxe_id,
                'exercice_fiscal_id' => $exerciceId,
                'periodicite_id'     => $periodiciteId,
                'montant'            => (string) $constat->ecart,
            ];
        }

        return $lignes;
    }

    /**
     * Génère les émissions de taxe complémentaires d'un redressement. Chaque
     * ligne précise la nature, l'exercice, la périodicité, le montant des droits
     * et, optionnellement, la pénalité. Le montant recouvrable de l'émission
     * vaut droits + pénalité ; la pénalité est conservée pour le détail.
     *
     * @param  array<int, array{nature_taxe_id:int, exercice_fiscal_id:int, periodicite_id:int, montant:string|float, penalite?:string|float}>  $lignes
     * @return Collection<int, EmissionTaxe>
     */
    public function genererEmissionsComplementaires(Redressement $redressement, array $lignes, User $user): Collection
    {
        $controle     = $redressement->controleFiscal;
        $etablissement = $controle->etablissement;
        $collectiviteId = $redressement->collectivite_id ?? Collectivite::value('id');

        $creees = DB::transaction(function () use ($redressement, $controle, $etablissement, $collectiviteId, $lignes, $user) {
            $creees = collect();

            foreach ($lignes as $ligne) {
                $exercice = ExerciceFiscal::findOrFail($ligne['exercice_fiscal_id']);
                $annee    = $exercice->annee;

                $dernier = EmissionTaxe::where('numero_emission', 'like', "EMI{$annee}%")
                    ->orderBy('numero_emission', 'desc')->value('numero_emission');
                $seq = $dernier ? ((int) substr($dernier, -6) + 1) : 1;

                $droits   = (string) $ligne['montant'];
                $penalite = (string) ($ligne['penalite'] ?? '0');
                $montant  = bcadd($droits, $penalite, 2); // total recouvrable de la déclaration

                $creees->push(EmissionTaxe::create([
                    'numero_emission'    => "EMI{$annee}" . str_pad($seq, 6, '0', STR_PAD_LEFT),
                    'numero_fiche'       => "FE{$annee}" . str_pad($seq, 6, '0', STR_PAD_LEFT),
                    'numero_article'     => $etablissement->numero . '/' . $annee,
                    'etablissement_id'   => $controle->etablissement_id,
                    'collectivite_id'    => $collectiviteId,
                    'redressement_id'    => $redressement->id,
                    'nature_taxe_id'     => $ligne['nature_taxe_id'],
                    'periodicite_id'     => $ligne['periodicite_id'],
                    'exercice_fiscal_id' => $exercice->id,
                    'montant_annuel'     => $montant,
                    'montant_periode'    => $montant,
                    'montant_prorata'    => $montant,
                    'penalite'           => $penalite,
                    'date_liquidation'   => now()->toDateString(),
                    'created_by'         => $user->id,
                ]));
            }

            return $creees;
        });

        $this->recalculerTotaux($redressement);

        return $creees;
    }

    /**
     * Recalcule les totaux du redressement à partir de ses déclarations :
     * droits = Σ(montant − pénalité), pénalités = Σ pénalité, total = Σ montant.
     */
    public function recalculerTotaux(Redressement $redressement): void
    {
        $emissions   = $redressement->emissionsTaxe()->get(['montant_annuel', 'penalite']);
        $totalMontant = $emissions->reduce(fn ($c, $e) => bcadd($c, (string) $e->montant_annuel, 2), '0');
        $penalites    = $emissions->reduce(fn ($c, $e) => bcadd($c, (string) $e->penalite, 2), '0');
        $droits       = bcsub($totalMontant, $penalites, 2);

        $redressement->update([
            'montant_droits'    => $droits,
            'montant_penalites' => $penalites,
            'montant_total'     => $totalMontant,
        ]);
    }
}
