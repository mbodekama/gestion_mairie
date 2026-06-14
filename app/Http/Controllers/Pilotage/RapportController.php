<?php

namespace App\Http\Controllers\Pilotage;

use App\Http\Controllers\Controller;

class RapportController extends Controller
{
    public function index()
    {
        return view('pilotage.rapports.index');
    }
}
