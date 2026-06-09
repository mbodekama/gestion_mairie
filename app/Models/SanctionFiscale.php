<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanctionFiscale extends Model
{
    protected $table = 'sanction_fiscale';
    public $timestamps = false;
    protected $guarded = ['id'];
}
