<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Campagnes de mails groupés adressés aux contribuables.
 *
 * Une campagne mémorise l'objet/le message, les critères de ciblage (filtre
 * contribuable), la date de planification (created_at), la date prévue pour
 * l'envoi et son statut. L'envoi effectif est porté par un job différé
 * (App\Jobs\EnvoyerCampagneMail) déclenché à la date prévue.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campagne_mail', function (Blueprint $table) {
            $table->id();
            // N° d'ordre métier (ex : CMP-2026-000001)
            $table->string('numero', 20)->unique();
            $table->foreignId('collectivite_id')->constrained('collectivite');
            $table->string('objet', 150);
            $table->text('message');
            // Critères de ciblage (snapshot du filtre contribuable) re-résolus à l'envoi
            $table->json('criteres')->nullable();
            // Nombre de contribuables ciblés au moment de la planification
            $table->integer('nombre_cibles')->default(0);
            // Nombre de mails effectivement mis en file à l'envoi
            $table->integer('nombre_envoyes')->default(0);
            // EN_ATTENTE | EN_COURS | ENVOYE | ECHEC
            $table->string('statut', 16)->default('EN_ATTENTE');
            $table->timestampTz('date_envoi_prevue');
            $table->timestampTz('date_envoi_effective')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestampsTz();
        });

        DB::statement('CREATE INDEX ix_campagne_mail_statut ON campagne_mail(statut)');
        DB::statement('CREATE INDEX ix_campagne_mail_prevue ON campagne_mail(date_envoi_prevue)');
        DB::statement('CREATE TRIGGER tg_campagne_mail BEFORE UPDATE ON campagne_mail FOR EACH ROW EXECUTE FUNCTION trg_set_updated_at()');
    }

    public function down(): void
    {
        Schema::dropIfExists('campagne_mail');
    }
};
