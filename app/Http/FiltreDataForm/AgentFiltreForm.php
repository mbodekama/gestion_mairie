<?php

namespace App\Http\FiltreDataForm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AgentFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $matricule      = null,
        public readonly ?string $nom            = null,
        public readonly ?int    $serviceId      = null,
        public readonly ?int    $fonctionAgentId = null,
        public readonly ?int    $gradeAgentId   = null,
        public readonly ?string $actif          = null,
    ) {}

    public static function regles(): array
    {
        return [
            'matricule'        => ['nullable', 'string', 'max:32'],
            'nom'              => ['nullable', 'string', 'max:128'],
            'service_id'       => ['nullable', 'integer', 'min:1'],
            'fonction_agent_id'=> ['nullable', 'integer', 'min:1'],
            'grade_agent_id'   => ['nullable', 'integer', 'min:1'],
            'actif'            => ['nullable', 'in:0,1'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);

        return new static(
            matricule:       $request->input('matricule'),
            nom:             $request->input('nom'),
            serviceId:       $request->filled('service_id')        ? (int) $request->input('service_id')        : null,
            fonctionAgentId: $request->filled('fonction_agent_id') ? (int) $request->input('fonction_agent_id') : null,
            gradeAgentId:    $request->filled('grade_agent_id')    ? (int) $request->input('grade_agent_id')    : null,
            actif:           $request->input('actif'),
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->matricule)) {
            $query->where('matricule', 'ilike', "%{$this->matricule}%");
        }

        if (filled($this->nom)) {
            $terme = "%{$this->nom}%";
            $query->where(function (Builder $q) use ($terme) {
                $q->where('nom', 'ilike', $terme)
                  ->orWhere('prenoms', 'ilike', $terme);
            });
        }

        if (filled($this->serviceId)) {
            $query->where('service_id', $this->serviceId);
        }

        if (filled($this->fonctionAgentId)) {
            $query->where('fonction_agent_id', $this->fonctionAgentId);
        }

        if (filled($this->gradeAgentId)) {
            $query->where('grade_agent_id', $this->gradeAgentId);
        }

        if ($this->actif !== null && $this->actif !== '') {
            $query->where('actif', (bool) $this->actif);
        }

        return $query;
    }
}
