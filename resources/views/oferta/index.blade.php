@extends('layouts.app')

@section('content')
@include('partials.ofertas-styles')

<div class="container">
   <h1>Ofertas Disponibles</h1>

   <!-- Formulario de filtrado -->
   <form method="GET" action="{{ route('oferta.index') }}" class="mb-4">
      <div class="d-flex">
         <!-- Filtro por Estado -->
         <div class="me-3">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" class="form-control">
               <option value="">Selecciona un estado</option>
               <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
               <option value="aceptada" {{ request('estado') == 'aceptada' ? 'selected' : '' }}>Aceptada</option>
               <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
               <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
            </select>
         </div>

         <!-- Filtro por Precio Mínimo -->
         <div class="me-3">
            <label for="precio_min">Precio Mínimo</label>
            <input type="number" name="precio_min" id="precio_min" class="form-control" value="{{ request('precio_min') }}" placeholder="Precio Mínimo">
         </div>

         <!-- Filtro por Precio Máximo -->
         <div class="me-3">
            <label for="precio_max">Precio Máximo</label>
            <input type="number" name="precio_max" id="precio_max" class="form-control" value="{{ request('precio_max') }}" placeholder="Precio Máximo">
         </div>

         <!-- Filtro por Material -->
         <div class="me-3">
            <label for="material">Material</label>
            <select name="material" id="material" class="form-control">
               <option value="">Selecciona un material</option>
               @foreach($materiales as $material)
               <option value="{{ $material->material }}" {{ request('material') == $material->material ? 'selected' : '' }}>
                  {{ ucfirst($material->material) }}
               </option>
               @endforeach
            </select>
         </div>


         <!-- Botón de Filtrado -->
         <button type="submit" class="btn btn-primary mt-4">Filtrar</button>
      </div>
   </form>

   <div class="d-flex justify-content-start mb-4">
      <a href="{{ route('oferta.create') }}" class="btn btn-success">Agregar Oferta</a>
   </div>

   <!-- Mensajes de éxito y error -->
   @if(session('success'))
   <div class="alert alert-success">{{ session('success') }}</div>
   @endif

   @if(session('error'))
   <div class="alert alert-danger">{{ session('error') }}</div>
   @endif

   <div class="table-responsive">
      <table class="table table-striped table-bordered">
         <thead class="thead-dark">
            <tr>
               <th>ID</th>
               <th>Usuario</th>
               <th>Dirección</th>
               <th>Material</th>
               <th>Cantidad</th>
               <th>Precio</th>
               <th>Imagen</th>
               <th>Estado</th>
               <th>Acciones</th>
            </tr>
         </thead>
         <tbody>
            @foreach($ofertas as $oferta)
            <tr>
               <td>{{ $oferta->id }}</td>
               <td>{{ $oferta->usuario->name }}</td>
               <td>{{ $oferta->direccion }}</td>
               <td>{{ $oferta->material }}</td>
               <td>{{ $oferta->cantidad }}</td>
               <td>Bs{{ number_format($oferta->precio, 2) }}</td>
               <td>
                  @if($oferta->image && Storage::disk('public')->exists($oferta->image))
                  <img src="{{ asset('storage/' . $oferta->image) }}" style="width: 80px; height: auto;">
                  @else
                  Sin imagen
                  @endif
               </td>
               <td>{{ ucfirst($oferta->estado) }}</td>
               <td>
                  <!-- Botón para Ver Oferta -->
                  <a href="{{ route('oferta.show', $oferta->id) }}" class="btn btn-info btn-sm">Ver</a>

                  @if(auth()->user()->id == $oferta->usuario_id)
                  @if($oferta->estado === 'completada')
                  <!-- Botón de advertencia para "editar" en estado completado -->
                  <button type="button" class="btn btn-warning btn-sm" onclick="alert('No se puede editar una oferta completada.');">Editar</button>
                  @else
                  <!-- Botón para Editar Oferta -->
                  <a href="{{ route('oferta.edit', $oferta->id) }}" class="btn btn-warning btn-sm">Editar</a>
                  @endif
                  @endif

                  @if(auth()->user()->id == $oferta->usuario_id)
                  @if($oferta->estado === 'completada')
                  <!-- Botón de advertencia para "eliminar" en estado completado -->
                  <button type="button" class="btn btn-danger btn-sm" onclick="alert('No se puede eliminar una oferta completada.');">Eliminar</button>
                  @else
                  <!-- Formulario para Eliminar Oferta -->
                  <form action="{{ route('oferta.destroy', $oferta->id) }}" method="POST" style="display:inline;">
                     @csrf
                     @method('DELETE')
                     <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta oferta?')">Eliminar</button>
                  </form>
                  @endif
                  @endif


                  <!-- Acciones para Recolector -->
                  @if(Auth::user()->role === 'recolector')
                  @if ($oferta->estado === 'disponible')
                  <form action="{{ route('oferta.aceptar', $oferta->id) }}" method="POST" style="display:inline;">
                     @csrf
                     <button type="submit" class="btn btn-success btn-sm">Aceptar</button>
                  </form>
                  @elseif ($oferta->estado === 'aceptada')
                  <form action="{{ route('oferta.rechazar', $oferta->id) }}" method="POST" style="display:inline;">
                     @csrf
                     <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de rechazar esta oferta?')">Rechazar</button>
                  </form>
                  @endif
                  @endif
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>

   {{ $ofertas->links('vendor.pagination.bootstrap-4') }}
</div>

@endsection