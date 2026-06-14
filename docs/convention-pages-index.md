# Convention des pages Index

Ce document définit la structure, le design et les classes PHP/Blade standards des pages `index` du backoffice.
Toute nouvelle page index doit respecter ces conventions.

---

## 1. Structure générale d'une page index

```
<x-page-header>          ← encart de titre (composant réutilisable)
<x-filtre.card>          ← card filtre avec formulaire POST
<div class="card">       ← card datatable
    card-header          ← titre + badge total + bouton Ajouter
    card-body p-0        ← tableau responsive
    card-footer          ← pagination
<x-datatable.traitement-lots>   ← zone actions par lots (bas de page)
```

---

## 2. Composant `x-page-header`

**Fichier :** `resources/views/components/page-header.blade.php`

```blade
<x-page-header titre="Gestion des Contribuables" />
<x-page-header titre="Gestion des Établissements" sous-titre="Texte personnalisé" />
```

**Props :**
| Prop | Type | Défaut |
|---|---|---|
| `titre` | string | obligatoire |
| `sousTitre` | string | `Bienvenue, {nom} !` |

---

## 3. Card Filtre — composant `x-filtre.card`

**Fichier :** `resources/views/components/filtre/card.blade.php`

### Utilisation

```blade
<x-filtre.card
    :action="route('contribuables.filtre')"   {{-- route POST dédiée --}}
    :reset="route('contribuables.index')"      {{-- lien GET de réinitialisation --}}
    titre="Filtrer les contribuables selon vos critères">

    {{-- champs du filtre ici --}}

</x-filtre.card>
```

### Props
| Prop | Type | Défaut |
|---|---|---|
| `action` | string | obligatoire |
| `reset` | string | obligatoire |
| `titre` | string | `'Filtrer les données selon vos critères'` |

### Caractéristiques
- Formulaire en **POST** (avec `@csrf` interne).
- En-tête : `h5.mb-0` + icône `fa-filter text-primary` + chevron collapse (rotation CSS pure, pas de JS).
- Corps : grille `row g-2` avec champs en `col-md-3` (max 4 par ligne).
- Footer : bouton **Rechercher les données** (`btn-primary btn-sm`) en premier, puis **Réinitialiser le filtre** (`btn-outline-secondary btn-sm`).
- Le collapse/expand est géré par Bootstrap + CSS `.filtre-chevron`.

---

## 4. Champs de filtre — composants `x-filtre.*`

### 4a. `x-filtre.input`

**Fichier :** `resources/views/components/filtre/input.blade.php`

```blade
<x-filtre.input
    name="nom"
    label="Nom / Raison sociale"
    placeholder="Recherche par nom..."
    type="text"           {{-- optionnel, défaut: text --}}
    col="col-md-3"        {{-- optionnel, défaut: col-md-3 --}}
/>
```

- Classe CSS : `form-control form-control-lg`
- Repopulation automatique via `request($name)`

### 4b. `x-filtre.select`

**Fichier :** `resources/views/components/filtre/select.blade.php`

```blade
{{-- Depuis un modèle Eloquent --}}
<x-filtre.select
    name="regime_imposition_id"
    label="Régime d'imposition"
    :options="$regimes"
    option-label="libelle_court"
    option-label-fallback="libelle"   {{-- fallback si libelle_court est null --}}
    option-value="id"                 {{-- défaut: 'id' --}}
    placeholder="— Tous —"
/>

{{-- Avec valeurs enum (code string comme valeur) --}}
<x-filtre.select
    name="type_personne"
    :options="$typesPersonne"
    option-label="libelle"
    option-value="code"               {{-- utilise le code métier, pas l'id --}}
/>

{{-- Avec options manuelles (via slot) --}}
<x-filtre.select name="statut" label="État">
    <option value="ACTIF"  @selected(request('statut') === 'ACTIF')>Actif</option>
    <option value="RADIE"  @selected(request('statut') === 'RADIE')>Radié</option>
</x-filtre.select>
```

**Props :**
| Prop | Défaut | Description |
|---|---|---|
| `options` | `collect()` | Collection Eloquent |
| `optionLabel` | `'libelle'` | Propriété affichée |
| `optionLabelFallback` | `null` | Propriété de repli si label est null |
| `optionValue` | `'id'` | Propriété utilisée comme value |
| `placeholder` | `'— Tous —'` | Option vide |

- Classe CSS : `form-select form-select-lg mb-3`
- Repopulation automatique via `request($name)`

### 4c. `x-filtre.date`

**Fichier :** `resources/views/components/filtre/date.blade.php`

```blade
<x-filtre.date name="date_du" label="Date du" />
<x-filtre.date name="date_au" label="Date au" />
```

- Utilise **Flatpickr** (chargé une seule fois via `@once @push`)
- Format affiché : `d/m/Y` — Format serveur : `Y-m-d` (converti via `Carbon::createFromFormat`)
- Repopulation automatique : `Carbon::parse($valeur)->format('d/m/Y')`

---

## 5. Routes — convention filtre POST

Le filtre utilise **POST** pour éviter l'exposition des critères dans l'URL.
La route `store` (POST standard du resource) n'est pas touchée.

```php
// routes/web.php
Route::resource('contribuables', ContribuableController::class);

// Route POST dédiée au filtre (URI distincte de store)
Route::post('contribuables/filtre', [ContribuableController::class, 'index'])
    ->name('contribuables.filtre');
```

> Le bouton **Réinitialiser** pointe vers `route('contribuables.index')` (GET) — il efface tous les critères.

---

## 6. `FiltreDataForm` — classe de filtre

**Dossier :** `app/Http/FiltreDataForm/`

### Classe abstraite de base

```php
abstract class FiltreDataForm
{
    abstract public static function regles(): array;        // règles de validation Laravel
    abstract public static function fromRequest(Request $request): static;
    abstract public function appliquer(Builder $query): Builder;

    protected static function valider(Request $request): void; // déclenche validate()
    protected static function parseDate(?string $valeur): ?string; // d/m/Y → Y-m-d
}
```

### Créer un nouveau filtre

```php
// app/Http/FiltreDataForm/MonModeleFiltreForm.php
class MonModeleFiltreForm extends FiltreDataForm
{
    public function __construct(
        public readonly ?string $nom  = null,
        public readonly ?int    $typeId = null,
    ) {}

    public static function regles(): array
    {
        return [
            'nom'     => ['nullable', 'string', 'max:128'],
            'type_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public static function fromRequest(Request $request): static
    {
        static::valider($request);          // ← validation déclenchée ici

        return new static(
            nom:    $request->input('nom'),
            typeId: $request->filled('type_id') ? (int) $request->input('type_id') : null,
        );
    }

    public function appliquer(Builder $query): Builder
    {
        if (filled($this->nom)) {
            $query->where('nom', 'ilike', "%{$this->nom}%");
        }
        if (filled($this->typeId)) {
            $query->where('type_id', $this->typeId);
        }
        return $query;
    }
}
```

### Règles de validation par type de champ

| Type | Règles recommandées |
|---|---|
| Texte libre | `['nullable', 'string', 'max:128']` |
| ID entier (FK) | `['nullable', 'integer', 'min:1']` |
| Enum / code string | `['nullable', 'string', 'in:VAL1,VAL2,VAL3']` |
| Année | `['nullable', 'integer', 'digits:4', 'min:2000', 'max:2099']` |
| Date (Flatpickr) | `['nullable', 'date_format:d/m/Y']` |
| Date fin de plage | `['nullable', 'date_format:d/m/Y', 'after_or_equal:date_debut']` |
| Booléen (0/1) | `['nullable', 'in:0,1']` |

### Utilisation dans le contrôleur

```php
public function index(Request $request)
{
    $filtre = MonModeleFiltreForm::fromRequest($request);

    $resultats = $filtre->appliquer(
        MonModele::with('relation')->whereNull('supprime_le')
    )->orderBy($sortActuel, $dirActuelle)
     ->paginate(15)
     ->withQueryString();

    return view('mon-modele.index', compact('resultats', 'filtre', ...));
}
```

---

## 7. `SelectOptionsService` — chargement des selects

**Fichier :** `app/Services/SelectOptionsService.php`

Charge n'importe quel modèle Eloquent pour alimenter un `<select>`.
S'injecte via le constructeur du contrôleur.

```php
public function __construct(private SelectOptionsService $selectOptions) {}

// Utilisation
$regimes  = $this->selectOptions->charger(RegimeImposition::class, 'libelle_court');
$communes = $this->selectOptions->charger(Commune::class, 'libelle');

// Avec filtre WHERE
$communesABJ = $this->selectOptions->charger(Commune::class, 'libelle', ['district_id' => $id]);

// Avec scope Eloquent (ex: scopeActifs() défini sur le modèle)
$zones = $this->selectOptions->charger(Zone::class, 'code', scope: 'actifs');
```

**Signature complète :**
```php
public function charger(
    string  $modele,           // class-string du modèle Eloquent
    string  $ordre   = 'libelle', // colonne de tri
    array   $filtres = [],        // conditions WHERE
    ?string $scope   = null,      // scope local Eloquent
): Collection
```

---

## 8. Card Datatable

### En-tête

```blade
<div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">
        <span class="fas fa-users me-2 text-primary"></span>
        Liste des contribuables
        <span class="badge bg-secondary ms-2">{{ $contribuables->total() }}</span>
    </h5>
    <a href="{{ route('contribuables.create') }}" class="btn btn-primary">
        <span class="fas fa-plus me-1"></span>Nouveau contribuable
    </a>
</div>
```

### Tableau

```blade
<table class="table table-sm table-hover table-striped mb-0 fs-9">
```

### Colonnes et tri — composants `x-datatable.*`

**`x-datatable.th-check`** — colonne de sélection (en-tête) :
```blade
<x-datatable.th-check />
```
- Checkbox "tout sélectionner" avec état `indeterminate` automatique.
- Surbrillance des lignes cochées via classe `table-active`.
- JS chargé via `@once @push('scripts')` (une seule fois par page).

**`x-datatable.th-tri`** — en-tête de colonne triable :
```blade
<x-datatable.th-tri
    colonne="nom"
    label="Nom"
    :sort-actuel="$sortActuel"
    :dir-actuelle="$dirActuelle"
    class="text-end"    {{-- optionnel --}}
/>
```
- Génère un lien GET `?sort=colonne&dir=asc|desc` via `request()->fullUrlWithQuery()`.
- Icône `fa-sort-up` (actif asc) / `fa-sort-down` (actif desc) / `fa-sort` (inactif).

### Tri dans le contrôleur

```php
private const COLONNES_TRI = ['nom', 'statut', 'created_at', ...]; // whitelist anti-injection

public function index(Request $request)
{
    $sortActuel  = in_array($request->query('sort'), self::COLONNES_TRI, true)
                   ? $request->query('sort') : 'updated_at'; // défaut
    $dirActuelle = $request->query('dir') === 'asc' ? 'asc' : 'desc';

    // ...
    ->orderBy($sortActuel, $dirActuelle)

    return view('...', compact('sortActuel', 'dirActuelle', ...));
}
```

> **Colonnes non-triables :** colonnes calculées côté PHP (ex: `soldeDu()`), ou colonnes affichées
> via une double relation sans FK directe (ex: Contribuable dans Recouvrements).

### Checkbox par ligne

```blade
<td class="text-center">
    <input type="checkbox" name="selection[]"
           value="{{ $item->id }}"
           class="form-check-input ligne-check">
</td>
```

### Boutons d'action

| Action | Style | Icône |
|---|---|---|
| Voir | `btn-sm btn-outline-info` | `fa-eye` |
| Modifier | `btn-sm btn-outline-warning` | `fa-edit` |
| Supprimer | `btn-sm btn-outline-danger` | `fa-trash` |

Les boutons destructifs appellent `confirm()` via `onsubmit`.

---

## 9. Zone Traitement par lots — `x-datatable.traitement-lots`

**Fichier :** `resources/views/components/datatable/traitement-lots.blade.php`

Placée **après** la card datatable sur toutes les pages index.
Affiche un message "prochainement" tant que le slot est vide.

```blade
{{-- Vide (placeholder) --}}
<x-datatable.traitement-lots />

{{-- Avec boutons d'action --}}
<x-datatable.traitement-lots>
    <button class="btn btn-outline-warning btn-sm">
        <span class="fas fa-envelope me-1"></span>Envoyer les avis
    </button>
    <button class="btn btn-outline-danger btn-sm">
        <span class="fas fa-ban me-1"></span>Suspendre la sélection
    </button>
</x-datatable.traitement-lots>
```

---

## 10. Pagination

```blade
@if ($items->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Affichage de {{ $items->firstItem() }} à {{ $items->lastItem() }}
            sur {{ $items->total() }} élément(s)
        </small>
        {{ $items->links() }}
    </div>
@endif
```

Le tri est préservé dans la pagination via `->withQueryString()` (paramètres GET `sort` et `dir`).

---

## 11. Récapitulatif des fichiers de convention

| Fichier / Dossier | Rôle |
|---|---|
| `app/Http/FiltreDataForm/FiltreDataForm.php` | Classe abstraite de base des filtres |
| `app/Http/FiltreDataForm/*FiltreForm.php` | Un fichier par entité filtrée |
| `app/Services/SelectOptionsService.php` | Chargement générique des selects |
| `resources/views/components/filtre/card.blade.php` | Card filtre (wrapper formulaire) |
| `resources/views/components/filtre/input.blade.php` | Champ texte du filtre |
| `resources/views/components/filtre/select.blade.php` | Champ select du filtre |
| `resources/views/components/filtre/date.blade.php` | Champ date Flatpickr |
| `resources/views/components/page-header.blade.php` | Encart titre de page |
| `resources/views/components/datatable/th-tri.blade.php` | En-tête de colonne triable |
| `resources/views/components/datatable/th-check.blade.php` | En-tête colonne de sélection |
| `resources/views/components/datatable/traitement-lots.blade.php` | Zone actions par lots |
