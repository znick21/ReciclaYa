<?php

namespace App\Http\Controllers;

use App\Models\Oferta; // Asegúrate de importar el modelo Oferta
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user(); 
    
        if ($user->role == 'vendedor') {
            return view('oferta.create');
        } elseif ($user->role == 'recolector') {
            // Obtener las ofertas disponibles para recolectores
            $ofertas = Oferta::where('estado', 'disponible')->with('usuario')->paginate(10);
            return view('oferta.index', compact('ofertas')); // Pasar las ofertas a la vista
        } else {
            return redirect()->route('home');
        }
    }
}
