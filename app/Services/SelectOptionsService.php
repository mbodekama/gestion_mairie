<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Charge les options de n'importe quel modèle Eloquent pour alimenter
 * un <select> HTML. À injecter dans les contrôleurs via le constructeur
 * ou directement dans les méthodes (method injection).
 */
class SelectOptionsService
{
    /**
     * Retourne une collection prête à passer à x-filtre.select ou à compact().
     *
     * @param  class-string<Model>  $modele     Classe Eloquent cible
     * @param  string               $ordre      Colonne de tri (défaut : 'libelle')
     * @param  array<string,mixed>  $filtres    Conditions WHERE supplémentaires
     * @param  string|null          $scope      Nom d'un scope Eloquent local sans le préfixe 'scope'
     */
    public function charger(
        string  $modele,
        string  $ordre   = 'libelle',
        array   $filtres = [],
        ?string $scope   = null,
    ): Collection {
        $query = $modele::query();

        if ($scope !== null) {
            $query->{$scope}();
        }

        foreach ($filtres as $colonne => $valeur) {
            $query->where($colonne, $valeur);
        }

        return $query->orderBy($ordre)->get();
    }
}
