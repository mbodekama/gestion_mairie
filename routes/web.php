<?php

use App\Http\Controllers\ContribuableController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ControleFiscalController;
use App\Http\Controllers\ConvocationController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\EmissionTaxeController;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\ExerciceFiscalController;
use App\Http\Controllers\ExonerationController;
use App\Http\Controllers\Parametrage\BaremeTaxeController;
use App\Http\Controllers\Parametrage\RegimeImpositionController;
use App\Http\Controllers\Parametrage\StatutContribuableController;
use App\Http\Controllers\Parametrage\TypePersonneController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecouvrementController;
use App\Http\Controllers\Administration\AgentController;
use App\Http\Controllers\Administration\AuditController;
use App\Http\Controllers\Administration\JournalController;
use App\Http\Controllers\Administration\ParametreController;
use App\Http\Controllers\Pilotage\ObjectifController;
use App\Http\Controllers\Pilotage\ObligationController;
use App\Http\Controllers\Pilotage\RapportController;
use App\Http\Controllers\Referentiel\ActiviteController as ReferentielActiviteController;
use App\Http\Controllers\Referentiel\ParametrageController as ReferentielParametrageController;
use App\Http\Controllers\Referentiel\TerritorialController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'session.lock'])->name('dashboard');

Route::middleware(['auth', 'session.lock'])->group(function () {
    // Pièces jointes (transversal à tous les modèles)
    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('documents/{document}/telecharger', [DocumentController::class, 'telecharger'])->name('documents.telecharger');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

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
    Route::resource('recouvrements',     RecouvrementController::class);

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
    });

    // ===== Administration =====
    Route::resource('agents', AgentController::class);
    Route::post('agents/filtre', [AgentController::class, 'index'])->name('agents.filtre');
    Route::post('agents/export', [AgentController::class, 'export'])->name('agents.export');

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
        Route::resource('regimes-imposition',    RegimeImpositionController::class);

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
