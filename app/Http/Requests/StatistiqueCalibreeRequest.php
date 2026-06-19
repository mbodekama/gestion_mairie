<?php

namespace App\Http\Requests;

use App\Services\StatistiqueCalibreeService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation des critères du formulaire « Statistique calibrée ».
 */
class StatistiqueCalibreeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $objets       = array_keys(StatistiqueCalibreeService::OBJETS);
        $granularites = array_keys(StatistiqueCalibreeService::GRANULARITES);
        $diagrammes   = array_keys(StatistiqueCalibreeService::DIAGRAMMES);

        return [
            'objet'          => ['required', Rule::in($objets)],
            'objet_compare'  => ['nullable', Rule::in($objets), 'different:objet'],
            'date_debut'     => ['nullable', 'date_format:d/m/Y'],
            'date_fin'       => ['nullable', 'date_format:d/m/Y', 'after_or_equal:date_debut'],
            'granularite'    => ['required', Rule::in($granularites)],
            'type_diagramme' => ['required', Rule::in($diagrammes)],
        ];
    }

    public function attributes(): array
    {
        return [
            'objet'          => 'objet à analyser',
            'objet_compare'  => 'objet de comparaison',
            'date_debut'     => 'date de début',
            'date_fin'       => 'date de fin',
            'granularite'    => 'regroupement',
            'type_diagramme' => 'type de diagramme',
        ];
    }

    public function messages(): array
    {
        return [
            'objet_compare.different' => "L'objet de comparaison doit être différent de l'objet principal.",
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
        ];
    }
}
