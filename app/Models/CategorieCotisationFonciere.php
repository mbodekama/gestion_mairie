<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorieCotisationFonciere extends Model
{
    protected $table = 'categorie_cotisation_fonciere';
    public $timestamps = false;
    protected $guarded = ['id'];
}
