<?php

use App\Http\Controllers\ContribuableController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ControleController;
use App\Http\Controllers\ControleFiscalController;
use App\Http\Controllers\ConvocationController;
use App\Http\Controllers\RedressementController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\EmissionTaxeController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ExerciceFiscalController;
use App\Http\Controllers\ExonerationController;
use App\Http\Controllers\MailGroupeController;
use App\Http\Controllers\Parametrage\BaremeTaxeController;
use App\Http\Controllers\Parametrage\RegimeImpositionController;
use App\Http\Controllers\Parametrage\StatutContribuableController;
use App\Http\Controllers\Parametrage\TypePersonneController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecouvrementController;
use App\Http\Controllers\Administration\AgentController;
use App\Http\Controllers\Administration\ServiceController;
use App\Http\Controllers\Administration\AuditController;
use App\Http\Controllers\Administration\JournalController;
use App\Http\Controllers\Administration\ParametreController;
use App\Http\Controllers\Pilotage\ObjectifController;
use App\Http\Controllers\Pilotage\ObligationController;
use App\Http\Controllers\Pilotage\RapportController;
use App\Http\Controllers\Pilotage\StatistiqueController;
use App\Http\Controllers\Referentiel\ActiviteController as ReferentielActiviteController;
use App\Http\Controllers\Referentiel\ParametrageController as ReferentielParametrageController;
use App\Http\Controllers\Referentiel\TerritorialController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function (\App\Services\TableauBordService $tableauBord) {
    // Mono-mairie aujourd'hui : on cible la collectivité active. Lorsqu'un lien
    // utilisateur ↔ collectivité existera, le remplacer ici par celui de l'agent connecté.
    $collectiviteId = \Illuminate\Support\Facades\DB::table('collectivite')
        ->where('active', true)
        ->value('id');

    $recouvrements = $collectiviteId
        ? $tableauBord->recouvrementsDouzeDerniersMois((int) $collectiviteId)
        : ['labels' => [], 'montants' => [], 'total' => 0.0, 'mois_courant' => 0.0, 'mois_precedent' => 0.0];

    $indicateurs = $collectiviteId
        ? $tableauBord->indicateursCles((int) $collectiviteId)
        : [
            'contribuables_actifs' => 0, 'etablissements_actifs' => 0, 'exercice_annee' => null,
            'montant_emis' => 0.0, 'montant_recouvre' => 0.0, 'reste_a_recouvrer' => 0.0,
            'taux_recouvrement' => 0.0, 'nb_emissions' => 0,
        ];

    $repartitions = $collectiviteId
        ? $tableauBord->repartitions((int) $collectiviteId)
        : [
            'objectif' => ['montant' => 0.0, 'recouvre' => 0.0, 'taux' => 0.0],
            'natures_taxe' => ['labels' => [], 'montants' => []],
            'modes_reglement' => ['labels' => [], 'montants' => []],
            'personnes' => ['physiques' => 0, 'morales' => 0],
        ];

    $topContribuables = $collectiviteId
        ? $tableauBord->topContribuables((int) $collectiviteId)
        : [];

    $emissions = $collectiviteId
        ? $tableauBord->emissionsDouzeDerniersMois((int) $collectiviteId)
        : ['labels' => [], 'montants' => [], 'total' => 0.0, 'mois_courant' => 0.0, 'mois_precedent' => 0.0];

    return view('dashboard', compact('recouvrements', 'indicateurs', 'repartitions', 'topContribuables', 'emissions'));
})->middleware(['auth', 'verified', 'session.lock'])->name('dashboard');

Route::middleware(['auth', 'session.lock'])->group(function () {
    // Notifications in-app (cloche du bandeau)
    Route::get('notifications/{id}/lire', [NotificationController::class, 'lire'])->name('notifications.lire');
    Route::post('notifications/tout-lire', [NotificationController::class, 'toutLire'])->name('notifications.tout-lire');

    // Pièces jointes (transversal à tous les modèles)
    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('documents/{document}/telecharger', [DocumentController::class, 'telecharger'])->name('documents.telecharger');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    // ===== Mails groupés aux contribuables =====
    // Déclarées avant la resource pour que « contribuables/mails-groupes » ne soit
    // pas capturé par la route show « contribuables/{contribuable} ».
    Route::get('contribuables/mails-groupes', [MailGroupeController::class, 'index'])
         ->middleware('can:CONTRIB_MAILS')->name('contribuables.mails-groupes.index');
    Route::post('contribuables/mails-groupes/recherche', [MailGroupeController::class, 'index'])
         ->middleware('can:CONTRIB_MAILS')->name('contribuables.mails-groupes.recherche');
    Route::get('contribuables/mails-groupes/nouvelle', [MailGroupeController::class, 'create'])
         ->middleware('can:CONTRIB_MAILS')->name('contribuables.mails-groupes.create');
    Route::post('contribuables/mails-groupes/nouvelle', [MailGroupeController::class, 'create'])
         ->middleware('can:CONTRIB_MAILS')->name('contribuables.mails-groupes.filtre');
    Route::post('contribuables/mails-groupes', [MailGroupeController::class, 'store'])
         ->middleware('can:CONTRIB_MAILS')->name('contribuables.mails-groupes.store');

    // Resources (GET index + toutes les autres actions)
    Route::resource('contribuables',     ContribuableController::class);
    Route::resource('etablissements',    EtablissementController::class);
    // Zones fiscales d'une commune (select dépendant du formulaire établissement)
    Route::get('etablissements/zones-fiscales/{commune}', [EtablissementController::class, 'zonesFiscales'])
         ->name('etablissements.zones-fiscales');
    Route::resource('exercices-fiscaux', ExerciceFiscalController::class)
         ->parameters(['exercices-fiscaux' => 'exerciceFiscal']);
    Route::post('exercices-fiscaux/{exerciceFiscal}/cloturer', [ExerciceFiscalController::class, 'cloturer'])->name('exercices-fiscaux.cloturer');
    Route::resource('emissions',         EmissionTaxeController::class);
    // Calcul des montants depuis le barème (bouton « Calculer » du formulaire émission)
    Route::post('emissions/liquider', [EmissionTaxeController::class, 'liquider'])->name('emissions.liquider');
    Route::get('emissions/{emission}/avis', [EmissionTaxeController::class, 'avis'])->name('emissions.avis');
    Route::resource('recouvrements',     RecouvrementController::class);
    // Quittance PDF d'un règlement (regroupe les règlements de la même quittance)
    Route::get('recouvrements/{recouvrement}/quittance', [RecouvrementController::class, 'quittance'])
         ->name('recouvrements.quittance');
    Route::post('recouvrements/{recouvrement}/annuler', [RecouvrementController::class, 'annuler'])
         ->name('recouvrements.annuler');

    // Routes POST dédiées à la soumission des filtres (URL distincte de store)
    Route::post('contribuables/filtre',     [ContribuableController::class,  'index'])->name('contribuables.filtre');
    Route::post('etablissements/filtre',    [EtablissementController::class, 'index'])->name('etablissements.filtre');
    Route::post('exercices-fiscaux/filtre', [ExerciceFiscalController::class,'index'])->name('exercices-fiscaux.filtre');
    Route::post('emissions/filtre',         [EmissionTaxeController::class,  'index'])->name('emissions.filtre');
    Route::post('recouvrements/filtre',     [RecouvrementController::class,  'index'])->name('recouvrements.filtre');

    // Routes POST d'export Excel (reçoivent les mêmes paramètres de filtre)
    Route::post('contribuables/export',     [ContribuableController::class,  'export'])->name('contribuables.export');
    Route::post('etablissements/export',    [EtablissementController::class, 'export'])->name('etablissements.export');
    Route::post('exercices-fiscaux/export', [ExerciceFiscalController::class,'export'])->name('exercices-fiscaux.export');
    Route::post('emissions/export',         [EmissionTaxeController::class,  'export'])->name('emissions.export');
    Route::post('recouvrements/export',     [RecouvrementController::class,  'export'])->name('recouvrements.export');

    // ===== Contrôle fiscal =====
    Route::resource('controle-fiscal', ControleFiscalController::class)
         ->parameters(['controle-fiscal' => 'controleFiscal']);
    Route::post('controle-fiscal/filtre', [ControleFiscalController::class, 'index'])->name('controle-fiscal.filtre');
    Route::post('controle-fiscal/export', [ControleFiscalController::class, 'export'])->name('controle-fiscal.export');

    // ===== Gestion du Contrôle (workflow) =====
    Route::get('controles', [ControleController::class, 'index'])->middleware('can:CONTROLE_CONSULTER')->name('controles.index');
    Route::post('controles/filtre', [ControleController::class, 'index'])->middleware('can:CONTROLE_CONSULTER')->name('controles.filtre');
    Route::post('controles/export', [ControleController::class, 'export'])->middleware('can:CONTROLE_CONSULTER')->name('controles.export');
    Route::get('controles/create', [ControleController::class, 'create'])->middleware('can:CONTROLE_INSTRUIRE')->name('controles.create');
    Route::post('controles', [ControleController::class, 'store'])->middleware('can:CONTROLE_INSTRUIRE')->name('controles.store');
    Route::get('controles/{controle}', [ControleController::class, 'show'])->middleware('can:CONTROLE_CONSULTER')->name('controles.show');
    Route::get('controles/{controle}/edit', [ControleController::class, 'edit'])->middleware('can:CONTROLE_INSTRUIRE')->name('controles.edit');
    Route::match(['put', 'patch'], 'controles/{controle}', [ControleController::class, 'update'])->middleware('can:CONTROLE_INSTRUIRE')->name('controles.update');
    Route::delete('controles/{controle}', [ControleController::class, 'destroy'])->middleware('can:CONTROLE_INSTRUIRE')->name('controles.destroy');
    Route::get('controles/{controle}/convocation-pdf', [ControleController::class, 'convocationPdf'])->middleware('can:CONTROLE_CONSULTER')->name('controles.convocation.pdf');
    Route::get('controles/{controle}/pv-cloture', [ControleController::class, 'pvCloture'])->middleware('can:CONTROLE_CONSULTER')->name('controles.pv-cloture');
    Route::get('controles/{controle}/rapport', [ControleController::class, 'rapport'])->middleware('can:CONTROLE_EXECUTER')->name('controles.rapport');
    Route::post('controles/{controle}/rapport', [ControleController::class, 'rapportStore'])->middleware('can:CONTROLE_EXECUTER')->name('controles.rapport.store');
    // Transition générique : la permission précise est vérifiée par le service selon la transition.
    Route::post('controles/{controle}/transition', [ControleController::class, 'transition'])->name('controles.transition');

    // ===== Redressements =====
    Route::get('redressements', [RedressementController::class, 'index'])->middleware('can:REDRESS_CONSULTER')->name('redressements.index');
    Route::post('redressements/filtre', [RedressementController::class, 'index'])->middleware('can:REDRESS_CONSULTER')->name('redressements.filtre');
    Route::post('redressements/export', [RedressementController::class, 'export'])->middleware('can:REDRESS_CONSULTER')->name('redressements.export');
    Route::get('redressements/{redressement}', [RedressementController::class, 'show'])->middleware('can:REDRESS_CONSULTER')->name('redressements.show');
    Route::get('redressements/{redressement}/avis', [RedressementController::class, 'avis'])->middleware('can:REDRESS_CONSULTER')->name('redressements.avis');
    Route::post('redressements/{redressement}/emissions', [RedressementController::class, 'emissions'])->middleware('can:REDRESS_GERER')->name('redressements.emissions');
    Route::patch('redressements/{redressement}/penalites', [RedressementController::class, 'penalites'])->middleware('can:REDRESS_GERER')->name('redressements.penalites');
    Route::patch('redressements/{redressement}/etat', [RedressementController::class, 'etat'])->middleware('can:REDRESS_GERER')->name('redressements.etat');

    Route::resource('exonerations', ExonerationController::class);
    Route::post('exonerations/filtre', [ExonerationController::class, 'index'])->name('exonerations.filtre');
    Route::post('exonerations/export', [ExonerationController::class, 'export'])->name('exonerations.export');

    // ===== Dossiers =====
    Route::resource('dossiers', DossierController::class);
    Route::post('dossiers/filtre', [DossierController::class, 'index'])->name('dossiers.filtre');
    Route::post('dossiers/export', [DossierController::class, 'export'])->name('dossiers.export');

    Route::resource('convocations', ConvocationController::class);
    Route::post('convocations/filtre', [ConvocationController::class, 'index'])->name('convocations.filtre');
    Route::post('convocations/export', [ConvocationController::class, 'export'])->name('convocations.export');

    // ===== Référentiel =====
    Route::prefix('referentiel')->name('referentiel.')->group(function () {
        Route::resource('territorial', TerritorialController::class)
             ->parameters(['territorial' => 'commune']);
        Route::post('territorial/filtre', [TerritorialController::class, 'index'])->name('territorial.filtre');
        Route::post('territorial/export', [TerritorialController::class, 'export'])->name('territorial.export');

        Route::resource('activites', ReferentielActiviteController::class);
        Route::post('activites/filtre', [ReferentielActiviteController::class, 'index'])->name('activites.filtre');
        Route::post('activites/export', [ReferentielActiviteController::class, 'export'])->name('activites.export');

        Route::resource('parametrage', ReferentielParametrageController::class)
             ->parameters(['parametrage' => 'natureTaxe']);
        Route::post('parametrage/filtre', [ReferentielParametrageController::class, 'index'])->name('parametrage.filtre');
        Route::post('parametrage/export', [ReferentielParametrageController::class, 'export'])->name('parametrage.export');
    });

    // ===== Pilotage =====
    Route::prefix('pilotage')->name('pilotage.')->group(function () {
        Route::resource('objectifs', ObjectifController::class);
        Route::post('objectifs/filtre', [ObjectifController::class, 'index'])->name('objectifs.filtre');
        Route::post('objectifs/export', [ObjectifController::class, 'export'])->name('objectifs.export');

        Route::resource('obligations', ObligationController::class);
        Route::post('obligations/filtre', [ObligationController::class, 'index'])->name('obligations.filtre');
        Route::post('obligations/export', [ObligationController::class, 'export'])->name('obligations.export');

        Route::get('rapports', [RapportController::class, 'index'])->name('rapports.index');
        Route::get('rapports/exonerations', [RapportController::class, 'exonerations'])->name('rapports.exonerations');

        Route::get('statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');
        Route::get('statistiques/calibree', [StatistiqueController::class, 'calibree'])->name('statistiques.calibree');
        Route::post('statistiques/calibree', [StatistiqueController::class, 'calibreeGenerer'])->name('statistiques.calibree.generer');
    });

    // ===== Administration =====
    Route::resource('agents', AgentController::class);
    Route::post('agents/filtre', [AgentController::class, 'index'])->name('agents.filtre');
    Route::post('agents/export', [AgentController::class, 'export'])->name('agents.export');

    Route::resource('services', ServiceController::class);
    Route::post('services/filtre', [ServiceController::class, 'index'])->name('services.filtre');
    Route::post('services/export', [ServiceController::class, 'export'])->name('services.export');

    Route::prefix('administration')->name('administration.')->group(function () {
        Route::resource('journal', JournalController::class)->only(['index']);
        Route::post('journal/filtre', [JournalController::class, 'index'])->name('journal.filtre');
        Route::post('journal/export', [JournalController::class, 'export'])->name('journal.export');

        Route::resource('audit', AuditController::class)->only(['index']);
        Route::post('audit/filtre', [AuditController::class, 'index'])->name('audit.filtre');
        Route::post('audit/export', [AuditController::class, 'export'])->name('audit.export');

        Route::resource('parametres', ParametreController::class);
        Route::post('parametres/filtre', [ParametreController::class, 'index'])->name('parametres.filtre');
        Route::post('parametres/export', [ParametreController::class, 'export'])->name('parametres.export');
    });

    // ===== Paramétrage référentiel contribuable =====
    Route::prefix('parametrage')->name('parametrage.')->group(function () {
        Route::resource('types-personne',        TypePersonneController::class);
        Route::resource('statuts-contribuable',  StatutContribuableController::class);
        Route::resource('regimes-imposition',    RegimeImpositionController::class)
             ->parameters(['regimes-imposition' => 'regimeImposition'])
             ->except(['show']);

        // Barèmes de taxe proportionnelle (patente, TEN…)
        Route::resource('baremes-taxe', BaremeTaxeController::class)
             ->parameters(['baremes-taxe' => 'baremeTaxe']);
        Route::post('baremes-taxe/filtre', [BaremeTaxeController::class, 'index'])->name('baremes-taxe.filtre');
        Route::post('baremes-taxe/export', [BaremeTaxeController::class, 'export'])->name('baremes-taxe.export');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
