@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Ofertas Disponibles</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('oferta.create') }}" class="btn btn-primary">Crear Nueva Oferta</a>
    </div>

    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="number" name="precio_min" class="form-control" placeholder="Precio Mínimo">
            </div>
            <div class="col-md-4">
                <input type="number" name="precio_max" class="form-control" placeholder="Precio Máximo">
            </div>
            <div class="col-md-4">
                <select name="estado" class="form-control">
                    <option value="">Estado</option>
                    <option value="disponible">Disponible</option>
                    <option value="aceptada">Aceptada</option>
                    <option value="rechazada">Rechazada</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-secondary mt-2">Filtrar</button>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Latitud</th>
                <th>Longitud</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ofertas as $oferta)
                <tr>
                    <td>{{ $oferta->id }}</td>
                    <td>{{ $oferta->usuario->name }}</td>
                    <td>{{ $oferta->latitud }}</td>
                    <td>{{ $oferta->longitud }}</td>
                    <td>{{ $oferta->cantidad }}</td>
                    <td>{{ $oferta->precio }}</td>
                    <td>{{ $oferta->estado }}</td>
                    <td>
                        <a href="{{ route('oferta.show', $oferta) }}" class="btn btn-info btn-sm">Ver</a>
                        @if(Auth::user()->role === 'recolector' && $oferta->estado === 'disponible')
                            <form action="{{ route('oferta.aceptar', $oferta) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Aceptar</button>
                            </form>
                            <form action="{{ route('oferta.rechazar', $oferta) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Rechazar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $ofertas->links() }} <!-- Paginación -->
</div>
@endsection
