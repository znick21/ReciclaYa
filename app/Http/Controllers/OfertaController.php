<?php

namespace App\Http\Controllers;

use App\Models\Oferta;
use App\Models\User;
use App\Models\HistorialOferta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfertaController extends Controller
{
    // Mostrar el formulario para crear una nueva oferta (Solo para vendedores)
    public function create()
    {
        // Verificar que el usuario sea vendedor
        if (Auth::user()->role !== 'vendedor') {
            return redirect()->route('oferta.index')->withErrors(['role' => 'Solo los vendedores pueden crear ofertas.']);
        }

        return view('oferta.create');
    }

    // Guardar la nueva oferta creada por el vendedor
    public function store(Request $request)
    {
        $request->validate([
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'cantidad' => 'required|numeric',
            'precio' => 'required|numeric',
        ]);

        // Crear la oferta
        $oferta = Oferta::create([
            'usuario_id' => Auth::id(), // Vendedor (usuario autenticado)
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
            'cantidad' => $request->cantidad,
            'precio' => $request->precio,
        ]);

        return redirect()->route('oferta.index')->with('success', 'Oferta creada exitosamente.');
    }

    // Ver las ofertas disponibles para recolectores (y vendedores si es necesario)
    public function index(Request $request)
{
    $ofertas = Oferta::with('usuario')->query(); // Asegúrate de incluir la relación 'usuario'
    
    // Filtros de búsqueda
    if ($request->has('estado') && $request->estado != '') {
        $ofertas = $ofertas->where('estado', $request->estado);
    }
    
    if ($request->has('precio_min')) {
        $ofertas = $ofertas->where('precio', '>=', $request->precio_min);
    }
    
    if ($request->has('precio_max')) {
        $ofertas = $ofertas->where('precio', '<=', $request->precio_max);
    }
    
    // Paginación
    $ofertas = $ofertas->paginate(10);

    // Pasar la variable 'ofertas' a la vista
    return view('oferta.index', compact('ofertas')); // Asegúrate de que esto esté aquí
}


    // Aceptar una oferta como recolector
    public function aceptarOferta($id)
    {
        $oferta = Oferta::findOrFail($id);

        // Verificar si el usuario es un recolector
        if (Auth::user()->role !== 'recolector') {
            return back()->withErrors(['role' => 'Solo los recolectores pueden aceptar ofertas.']);
        }

        // Verificar si la oferta ya fue aceptada o rechazada
        if ($oferta->estado !== 'disponible') {
            return back()->withErrors(['estado' => 'La oferta ya ha sido aceptada o rechazada.']);
        }

        // Crear un registro en el historial
        HistorialOferta::create([
            'oferta_id' => $oferta->id,
            'recolector_id' => Auth::id(), // Recolector (usuario autenticado)
            'estado' => 'aceptada',
        ]);

        // Actualizar el estado de la oferta
        $oferta->estado = 'aceptada';
        $oferta->save();

        return redirect()->route('oferta.index')->with('success', 'Oferta aceptada exitosamente.');
    }

    // Rechazar una oferta como recolector
    public function rechazarOferta($id)
    {
        $oferta = Oferta::findOrFail($id);

        // Verificar si el usuario es un recolector
        if (Auth::user()->role !== 'recolector') {
            return back()->withErrors(['role' => 'Solo los recolectores pueden rechazar ofertas.']);
        }

        // Verificar si la oferta ya fue aceptada o rechazada
        if ($oferta->estado !== 'disponible') {
            return back()->withErrors(['estado' => 'La oferta ya ha sido aceptada o rechazada.']);
        }

        // Crear un registro en el historial
        HistorialOferta::create([
            'oferta_id' => $oferta->id,
            'recolector_id' => Auth::id(), // Recolector (usuario autenticado)
            'estado' => 'rechazada',
        ]);

        // Actualizar el estado de la oferta
        $oferta->estado = 'rechazada';
        $oferta->save();

        return redirect()->route('oferta.index')->with('error', 'Oferta rechazada.');
    }

    // Ver detalles de una oferta
    public function show($id)
    {
        $oferta = Oferta::with('usuario')->findOrFail($id);
        return view('oferta.show', compact('oferta'));
    }
}
