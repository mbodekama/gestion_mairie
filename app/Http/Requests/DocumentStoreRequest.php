<?php

namespace App\Http\Requests;

use App\Models\DocType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentStoreRequest extends FormRequest
{
    // Modèles autorisés à recevoir des pièces jointes
    public const MODELES_AUTORISES = [
        \App\Models\Contribuable::class,
        \App\Models\Etablissement::class,
        \App\Models\Dossier::class,
        \App\Models\ControleFiscal::class,
        \App\Models\Convocation::class,
        \App\Models\EmissionTaxe::class,
    ];

    // 10 Mo max par fichier
    private const TAILLE_MAX_KO = 10240;

    private const EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'xls', 'xlsx', 'odt', 'ods'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $modelType = $this->input('model_type', '');

        return [
            'model_type'  => ['required', 'string', Rule::in(self::MODELES_AUTORISES)],
            'model_id'    => ['required', 'integer', 'min:1'],
            'doc_type_id' => [
                'required',
                Rule::exists('doc_type', 'id')->where('model_type', $modelType),
            ],
            'nom'         => ['required', 'string', 'max:255'],
            'fichier'     => [
                'required',
                'file',
                'max:' . self::TAILLE_MAX_KO,
                'mimes:' . implode(',', self::EXTENSIONS),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fichier.max'   => 'Le fichier ne doit pas dépasser 10 Mo.',
            'fichier.mimes' => 'Format non autorisé. Formats acceptés : PDF, images, Word, Excel.',
            'doc_type_id.exists' => 'Type de document invalide pour ce modèle.',
        ];
    }
}
