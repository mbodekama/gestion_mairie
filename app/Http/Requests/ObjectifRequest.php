<?php

namespace App\Http\Requests;

use App\Models\ExerciceFiscal;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

/**
 * Création et mise à jour d'un objectif de recouvrement.
 *
 * L'objectif est rattaché à un exercice fiscal et couvre une période bornée
 * par les dates de cet exercice. L'année et la collectivité sont déduites de
 * l'exercice côté contrôleur (jamais saisies).
 */
class ObjectifRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'exercice_fiscal_id' => ['required', 'integer', 'exists:exercice_fiscal,id'],
            'periode_debut'      => ['required', 'date_format:d/m/Y'],
            'periode_fin'        => ['required', 'date_format:d/m/Y', 'after_or_equal:periode_debut'],
            'montant'            => ['required', 'numeric', 'min:0'],
            'montant_revise'     => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Données validées prêtes pour Eloquent : les dates de période passent du
     * format de saisie d/m/Y au format SQL Y-m-d.
     */
    public function donneesObjectif(): array
    {
        $donnees = $this->validated();

        foreach (['periode_debut', 'periode_fin'] as $champ) {
            $donnees[$champ] = Carbon::createFromFormat('d/m/Y', $donnees[$champ])->toDateString();
        }

        return $donnees;
    }

    /**
     * Contrôles dépendant de l'exercice choisi : exercice non clôturé et
     * période strictement comprise dans les bornes de l'exercice.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $exercice = ExerciceFiscal::find($this->input('exercice_fiscal_id'));

            if (! $exercice) {
                return;
            }

            // Un objectif déjà rattaché à cet exercice reste modifiable même
            // si l'exercice vient d'être clôturé ; sinon, exercice clôturé interdit.
            $objectif = $this->route('objectif');
            $dejaRattache = $objectif && (int) $objectif->exercice_fiscal_id === (int) $exercice->id;

            if ($exercice->cloture && ! $dejaRattache) {
                $validator->errors()->add(
                    'exercice_fiscal_id',
                    'Impossible de définir un objectif sur un exercice clôturé.'
                );
            }

            // Les dates arrivent au format d/m/Y (date_format) : parsing explicite.
            $debut = $this->dateValidee('periode_debut');
            $fin   = $this->dateValidee('periode_fin');

            if ($debut && $debut->lt($exercice->date_debut)) {
                $validator->errors()->add(
                    'periode_debut',
                    'La période doit débuter au plus tôt le ' . $exercice->date_debut->format('d/m/Y') . '.'
                );
            }

            if ($fin && $fin->gt($exercice->date_fin)) {
                $validator->errors()->add(
                    'periode_fin',
                    'La période doit se terminer au plus tard le ' . $exercice->date_fin->format('d/m/Y') . '.'
                );
            }
        });
    }

    /**
     * Parse une date d/m/Y saisie en Carbon, ou null si absente/mal formée
     * (le format est déjà contrôlé par la règle date_format).
     */
    private function dateValidee(string $champ): ?Carbon
    {
        $valeur = $this->input($champ);

        if (! $valeur) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $valeur);
        } catch (\Exception) {
            return null;
        }
    }

    public function messages(): array
    {
        return [
            'exercice_fiscal_id.required' => "L'exercice fiscal est obligatoire.",
            'periode_debut.required'      => 'La date de début de période est obligatoire.',
            'periode_fin.required'        => 'La date de fin de période est obligatoire.',
            'periode_fin.after_or_equal'  => 'La fin de période ne peut précéder le début.',
            'montant.required'            => 'Le montant objectif est obligatoire.',
            'montant.min'                 => 'Le montant doit être positif.',
        ];
    }
}
