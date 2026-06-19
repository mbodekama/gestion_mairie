<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation de la composition d'un envoi groupé d'e-mails aux contribuables.
 * Les champs de ciblage (filtre) sont validés séparément par
 * ContribuableFiltreForm lors de la résolution des destinataires.
 */
class MailGroupeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'objet'             => ['required', 'string', 'max:150'],
            'message'           => ['required', 'string', 'max:5000'],
            'date_envoi_prevue' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'objet.required'             => 'L\'objet du message est obligatoire.',
            'message.required'           => 'Le corps du message est obligatoire.',
            'date_envoi_prevue.required' => 'La date prévue pour l\'envoi est obligatoire.',
            'date_envoi_prevue.date'     => 'La date prévue pour l\'envoi est invalide.',
        ];
    }
}
