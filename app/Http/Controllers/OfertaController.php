<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Oferta;
use App\Models\User;
use App\Models\HistorialOferta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OfertaController extends Controller
{
    // Mostrar el formulario para crear una nueva oferta (Solo para vendedores)
    public function create()
    {
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
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'material' => 'required|string|max:255',
        ]);

        // Procesar la imagen
        $imagePath = null;
        if ($request->hasFile('imagen')) {
            // Guardar la imagen en public/images y obtener la ruta
            $imagePath = $request->file('imagen')->store('images', 'public');
            // Verificar si el archivo se guardó correctamente
            if (!$imagePath) {
                return back()->withErrors(['imagen' => 'Hubo un error al guardar la imagen.']);
            }
        }

        try {
            Oferta::create([
                'usuario_id' => Auth::id(),
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'cantidad' => $request->cantidad,
                'precio' => $request->precio,
                'image' => $imagePath,
                'material' => $request->material,
                'estado' => 'disponible', // Agregar el estado disponible
            ]);

            return redirect()->route('oferta.index')->with('success', 'Oferta creada exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear la oferta: ' . $e->getMessage()]);
        }
    }

    // Ver las ofertas disponibles para recolectores (y vendedores si es necesario)
    public function index(Request $request)
    {
        $user = Auth::user(); // Obtiene el usuario autenticado

        // Cargar ofertas con la relación de usuario
        $ofertas = Oferta::with('usuario');

        // Obtener materiales únicos de las ofertas
        $materiales = Oferta::select('material')->distinct()->get();

        // Filtrar ofertas según el rol del usuario
        if ($user->role == 'vendedor') {
            // Vendedores ven solo sus propias ofertas
            $ofertas->where('usuario_id', $user->id);
        } elseif ($user->role == 'recolector') {
            // Recolectores ven ofertas disponibles y las que han aceptado
            $ofertas->where(function ($query) {
                $query->where('estado', 'disponible')
                    ->orWhereHas('historial', function ($query) {
                        $query->where('recolector_id', Auth::id())
                            ->where('estado', 'aceptada');
                    });
            });
        }

        // Excluir ofertas completadas por defecto
        if (!$request->filled('estado') || $request->estado !== 'completada') {
            $ofertas->where('estado', '!=', 'completada');
        }

        // Filtro por Estado
        if ($request->filled('estado')) {
            $ofertas->where('estado', $request->estado);
        } else {
            // Excluir ofertas rechazadas por defecto
            $ofertas->where('estado', '!=', 'rechazada');
        }

        // Filtro por Precio Mínimo
        if ($request->filled('precio_min')) {
            $ofertas->where('precio', '>=', $request->precio_min);
        }

        // Filtro por Precio Máximo
        if ($request->filled('precio_max')) {
            $ofertas->where('precio', '<=', $request->precio_max);
        }


        // Filtro por Material
        if ($request->filled('material')) {
            $ofertas->where('material', $request->material);
        }

        // Paginación
        $ofertas = $ofertas->paginate(10);

        // Agregar direcciones a cada oferta
        foreach ($ofertas as $oferta) {
            $oferta->direccion = $this->getDireccion($oferta->latitud ?? null, $oferta->longitud ?? null, false);
        }

        return view('oferta.index', compact('ofertas', 'materiales'));
    }






    // Función para obtener la dirección a partir de la latitud y longitud
    private function getDireccion($latitud, $longitud, $completa = false)
    {
        $apiKey = 'AIzaSyDX8c0ulrUE5aUsefFR-EXM1NQlIAa8QyU';
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitud},{$longitud}&key={$apiKey}";

        try {
            $response = Http::get($url);

            if ($response->failed()) {
                Log::error('Error al obtener dirección', [
                    'url' => $url,
                    'response' => $response->body()
                ]);
                return 'Error al obtener dirección';
            }

            $data = $response->json();

            if (isset($data['results'][0]['formatted_address'])) {
                $direccion = $data['results'][0]['formatted_address'];
                return !$completa && strlen($direccion) > 100 ? substr($direccion, 0, 60) . '...' : $direccion;
            }

            return 'Dirección no disponible';
        } catch (\Exception $e) {
            Log::error('Excepción al obtener dirección', [
                'latitud' => $latitud,
                'longitud' => $longitud,
                'error' => $e->getMessage()
            ]);
            return 'Error al obtener dirección';
        }
    }

    // Aceptar una oferta como recolector
    public function aceptarOferta($id)
    {
        $oferta = Oferta::findOrFail($id); // Buscar la oferta por su ID

        if (Auth::user()->role !== 'recolector') {
            return back()->withErrors(['role' => 'Solo los recolectores pueden aceptar ofertas.']);
        }

        // Verifica si el estado de la oferta es 'disponible'
        if ($oferta->estado !== 'disponible') {
            return back()->withErrors(['estado' => 'La oferta ya ha sido aceptada o rechazada.']);
        }

        try {
            // Actualiza el estado de la oferta a 'aceptada'
            $oferta->estado = 'aceptada';
            $oferta->save(); // Guarda los cambios en la base de datos

            // Crea el historial de la oferta aceptada
            HistorialOferta::create([
                'oferta_id' => $oferta->id,
                'recolector_id' => Auth::id(),
                'estado' => 'aceptada',
            ]);

            // Redirige a la vista 'show' de la oferta con el ID
            return redirect()->route('oferta.show', ['id' => $oferta->id])->with('success', 'Oferta aceptada exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al aceptar la oferta: ' . $e->getMessage()]);
        }
    }

    // Rechazar una oferta como recolector

    // Rechazar una oferta como recolector
    public function rechazarOferta($id)
    {
        $oferta = Oferta::findOrFail($id); // Buscar la oferta por su ID

        // Verificar que el usuario autenticado es un recolector
        if (Auth::user()->role !== 'recolector') {
            return back()->withErrors(['role' => 'Solo los recolectores pueden rechazar ofertas.']);
        }

        // Verificar si el estado de la oferta es 'aceptada'
        if ($oferta->estado !== 'aceptada') {
            return back()->withErrors(['estado' => 'La oferta no puede ser rechazada, ya que no ha sido aceptada.']);
        }

        // Verificar si el recolector que intenta rechazar la oferta es el mismo que la aceptó
        $historial = HistorialOferta::where('oferta_id', $oferta->id)
            ->where('recolector_id', Auth::id())
            ->where('estado', 'aceptada')
            ->first();

        if (!$historial) {
            return back()->withErrors(['error' => 'No tienes permiso para rechazar esta oferta.']);
        }

        try {
            // Crea el historial de la oferta rechazada
            HistorialOferta::create([
                'oferta_id' => $oferta->id,
                'recolector_id' => Auth::id(),
                'estado' => 'rechazada',
            ]);

            // Actualiza el estado de la oferta a 'rechazada'
            $oferta->estado = 'rechazada';
            $oferta->save(); // Guarda los cambios en la base de datos

            // Redirige a la vista index con mensaje de error, ya que la oferta fue rechazada
            return redirect()->route('oferta.index')->with('error', 'Oferta rechazada.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al rechazar la oferta: ' . $e->getMessage()]);
        }
    }


    // Ver detalles de una oferta
    public function show($id)
{
    $oferta = Oferta::findOrFail($id); // Buscar la oferta por su ID

    // Verificar que el usuario autenticado es un recolector o el creador de la oferta
    if (Auth::user()->role !== 'recolector' && Auth::user()->id !== $oferta->usuario_id) {
        return back()->withErrors(['permission' => 'No tienes permiso para ver esta oferta.']);
    }

    // Verificar si el recolector que intenta ver la oferta es el mismo que la aceptó
    $historial = HistorialOferta::where('oferta_id', $oferta->id)
        ->where('recolector_id', Auth::id())
        ->where('estado', 'aceptada')
        ->first();

    // Si el usuario es un recolector y no está en el historial, solo el creador puede ver
    if (Auth::user()->role === 'recolector' && !$historial) {
        return back()->withErrors(['permission' => 'No tienes permiso para ver esta oferta.']);
    }

    // Obtener el recolector que aceptó la oferta (si hay)
    $recolector = null;
    if ($historial) {
        $recolector = User::find($historial->recolector_id); // Obtener los datos del recolector
    }

    // Obtener la dirección utilizando las coordenadas
    $direccion = $this->getDireccion($oferta->latitud ?? null, $oferta->longitud ?? null, false);

    // Retornar la vista con la oferta, el recolector y la dirección
    return view('oferta.show', compact('oferta', 'recolector', 'direccion'));
}


    



    // Mostrar el formulario para editar una oferta
    public function edit($id)
    {
        $oferta = Oferta::findOrFail($id);

        // Verificar si la oferta está completada
        if ($oferta->estado === 'completada') {
            return redirect()->route('oferta.index')->withErrors(['error' => 'No puedes editar una oferta completada.']);
        }

        // Verificar que el usuario autenticado es el creador de la oferta
        if ($oferta->usuario_id !== Auth::id()) {
            return redirect()->route('oferta.index')->withErrors(['error' => 'No tienes permiso para editar esta oferta.']);
        }

        return view('oferta.edit', compact('oferta'));
    }


    // Actualizar los datos de una oferta
    public function update(Request $request, $id)
    {
        // Buscar la oferta por ID
        $oferta = Oferta::findOrFail($id);

        // Verificar si la oferta ya está completada
        if ($oferta->estado == 'completada') {
            return redirect()->route('oferta.index')->withErrors(['error' => 'No puedes editar una oferta completada.']);
        }

        // Verificar que el usuario autenticado es el creador de la oferta
        if ($oferta->usuario_id !== Auth::id()) {
            return redirect()->route('oferta.index')->withErrors(['error' => 'No tienes permiso para editar esta oferta.']);
        }

        // Validación de los datos del formulario
        $request->validate([
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'cantidad' => 'required|numeric',
            'precio' => 'required|numeric',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'material' => 'required|string|max:255',
        ]);

        // Procesar la imagen
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior si existe
            if ($oferta->image) {
                Storage::delete('public/' . $oferta->image);
            }

            // Guardar la nueva imagen
            $imagePath = $request->file('imagen')->store('images', 'public');
        } else {
            $imagePath = $oferta->image; // Si no se sube una nueva imagen, mantener la anterior
        }

        try {
            // Actualizar la oferta con los nuevos datos
            $oferta->update([
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'cantidad' => $request->cantidad,
                'precio' => $request->precio,
                'image' => $imagePath,
                'material' => $request->material,
            ]);

            // Redirigir con mensaje de éxito
            return redirect()->route('oferta.index')->with('success', 'Oferta actualizada exitosamente.');
        } catch (\Exception $e) {
            // Si ocurre un error al actualizar, devolver el error
            return back()->withErrors(['error' => 'Error al actualizar la oferta: ' . $e->getMessage()]);
        }
    }


    // Eliminar una oferta
    // Eliminar una oferta
    public function destroy($id)
    {
        $oferta = Oferta::findOrFail($id);

        // Verificar que el usuario autenticado es el creador de la oferta
        if ($oferta->usuario_id !== Auth::id()) {
            return redirect()->route('oferta.index')->withErrors(['error' => 'No tienes permiso para eliminar esta oferta.']);
        }

        // Verificar si la oferta está completada
        if ($oferta->estado === 'completada') {
            return redirect()->route('oferta.index')->withErrors(['error' => 'No puedes eliminar una oferta completada.']);
        }

        try {
            // Eliminar la imagen si existe
            if ($oferta->image) {
                Storage::delete('public/' . $oferta->image);
            }

            // Eliminar la oferta
            $oferta->delete();

            return redirect()->route('oferta.index')->with('success', 'Oferta eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar la oferta: ' . $e->getMessage()]);
        }
    }


    // Completar una oferta como recolector
    // Completar una oferta como recolector
    public function completarOferta($id)
    {
        $oferta = Oferta::findOrFail($id); // Buscar la oferta por su ID

        // Verificar que el usuario autenticado es un recolector
        if (Auth::user()->role !== 'recolector') {
            return back()->withErrors(['role' => 'Solo los recolectores pueden completar ofertas.']);
        }

        // Verificar si el estado de la oferta es 'aceptada' (solo se pueden completar las ofertas aceptadas)
        if ($oferta->estado !== 'aceptada') {
            return back()->withErrors(['estado' => 'La oferta no puede ser completada, ya que no ha sido aceptada.']);
        }

        // Verificar si el recolector que intenta completar la oferta es el mismo que la aceptó
        $historial = HistorialOferta::where('oferta_id', $oferta->id)
            ->where('recolector_id', Auth::id())
            ->where('estado', 'aceptada')
            ->first();

        if (!$historial) {
            return back()->withErrors(['error' => 'No tienes permiso para completar esta oferta.']);
        }

        try {
            // Actualizar el estado de la oferta a 'completada'
            $oferta->estado = 'completada';
            $oferta->save(); // Guarda los cambios en la base de datos

            // Crear el historial de la oferta completada
            HistorialOferta::create([
                'oferta_id' => $oferta->id,
                'recolector_id' => Auth::id(),
                'estado' => 'completada',
            ]);

            // Redirige a la vista de detalles de la oferta con un mensaje de éxito
            return redirect()->route('oferta.show', ['id' => $oferta->id])->with('success', 'Oferta completada exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al completar la oferta: ' . $e->getMessage()]);
        }
    }
}
