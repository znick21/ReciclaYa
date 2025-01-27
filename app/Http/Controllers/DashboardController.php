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
        return redirect()->route('oferta.create'); // Redirigir a la creación de ofertas
    } elseif ($user->role == 'recolector') {
        return redirect()->route('oferta.index'); // Redirigir a la lista de ofertas
    } else {
        return redirect()->route('home'); // Redirigir a la página de inicio
    }
}

    
}
