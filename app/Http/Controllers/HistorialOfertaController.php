<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistorialOferta;
use App\Models\Oferta;
use Illuminate\Support\Facades\Auth;


class HistorialOfertaController extends Controller
{
    // Aceptar una oferta
    public function aceptar($id)
    {
        $oferta = Oferta::findOrFail($id);

        // Crear un historial de aceptación
        HistorialOferta::create([
            'oferta_id' => $oferta->id,
            'recolector_id' => Auth::id(),
            'estado' => 'aceptada',
        ]);

        return redirect()->route('mapa')->with('success', 'Oferta aceptada exitosamente');
    }

    // Rechazar una oferta
    public function rechazar($id)
    {
        $oferta = Oferta::findOrFail($id);

        // Crear un historial de rechazo
        HistorialOferta::create([
            'oferta_id' => $oferta->id,
            'recolector_id' => Auth::id(),
            'estado' => 'rechazada',
        ]);

        return redirect()->route('mapa')->with('error', 'Oferta rechazada');
    }
}
