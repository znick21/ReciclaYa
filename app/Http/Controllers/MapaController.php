<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Oferta;

class MapaController extends Controller
{
    // Mostrar el mapa con las ofertas de reciclaje
    public function showMap()
    {
        $ofertas = Oferta::all();
        return view('mapa', compact('ofertas'));
    }
}
