<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Création et mise à jour d'un contribuable (personne physique ou morale).
 *
 * Les tailles `max:` sont alignées sur les colonnes de la table `contribuable`
 * (migration 2026_06_08_000005) : la validation refuse proprement toute saisie
 * trop longue au lieu de laisser remonter une erreur SQL.
 *
 * Le `type_personne` est figé à la création et n'est plus modifiable ensuite ;
 * les règles `required_if` correspondantes ne s'appliquent donc qu'à la création.
 */
class ContribuableRequest extends FormRequest
{
    public function rules(): array
    {
        $contribuableId = optional($this->route('contribuable'))->id;
        $estCreation    = $contribuableId === null;

        $rules = [
            'numero_compte'        => ['nullable', 'string', 'max:12', Rule::unique('contribuable', 'numero_compte')->ignore($contribuableId)],
            'statut'               => ['required', 'string', 'max:12'],
            'regime_imposition_id' => ['nullable', 'integer', 'exists:regime_imposition,id'],
            // Contacts
            'telephone'            => ['nullable', 'string', 'max:30'],
            'cellulaire'           => ['nullable', 'string', 'max:30'],
            'fax'                  => ['nullable', 'string', 'max:30'],
            'email'                => ['nullable', 'email', 'max:150'],
            'boite_postale'        => ['nullable', 'string', 'max:50'],
            // Personne physique
            'nom'                  => ['nullable', 'string', 'max:64'],
            'prenoms'              => ['nullable', 'string', 'max:128'],
            'sexe'                 => ['nullable', 'in:M,F'],
            'date_naissance'       => ['nullable', 'date'],
            'lieu_naissance'       => ['nullable', 'string', 'max:64'],
            'nationalite_id'       => ['nullable', 'integer', 'exists:nationalite,id'],
            'nature_piece'         => ['nullable', 'string', 'max:20'],
            'numero_piece'         => ['nullable', 'string', 'max:50'],
            'nom_pere'             => ['nullable', 'string', 'max:64'],
            'prenoms_pere'         => ['nullable', 'string', 'max:128'],
            'nom_mere'             => ['nullable', 'string', 'max:64'],
            'prenoms_mere'         => ['nullable', 'string', 'max:128'],
            // Personne morale
            'raison_sociale'           => ['nullable', 'string', 'max:128'],
            'sigle'                    => ['nullable', 'string', 'max:20'],
            'denomination_commerciale' => ['nullable', 'string', 'max:200'],
            'forme_juridique_id'       => ['nullable', 'integer', 'exists:forme_juridique,id'],
            'registre_commerce'        => ['nullable', 'string', 'max:32'],
            'date_registre_commerce'   => ['nullable', 'date'],
            'ville_registre_commerce'  => ['nullable', 'string', 'max:64'],
            'nombre_associes'          => ['nullable', 'integer', 'min:0'],
            'capital_social'           => ['nullable', 'numeric', 'min:0'],
        ];

        if ($estCreation) {
            $rules['type_personne']    = ['required', 'in:PP,PM'];
            $rules['nom'][]            = 'required_if:type_personne,PP';
            $rules['raison_sociale'][] = 'required_if:type_personne,PM';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'numero_compte.unique'       => 'Ce numéro de compte est déjà utilisé par un autre contribuable.',
            'type_personne.required'     => 'Le type de personne est obligatoire.',
            'nom.required_if'            => 'Le nom est obligatoire pour une personne physique.',
            'raison_sociale.required_if' => 'La raison sociale est obligatoire pour une personne morale.',
        ];
    }
}
