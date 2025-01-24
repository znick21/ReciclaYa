@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Bienvenido al Dashboard</h2>
    <p>Esta es tu página principal.</p>

    <h3>Ofertas de reciclaje</h3>
    <a href="{{ route('oferta.create') }}">Crear nueva oferta</a>
    
    <h4>Mis ofertas:</h4>
    <ul>
        @foreach($ofertass as $ofertaa)
        <li>
            <p>Oferta de reciclaje ({{ $ofertaa->cantidad_latas }} kg) - Bs {{ $ofertaa->precio }}</p>
            <a href="{{ route('oferta.edit', $ofertaa->id) }}">Editar</a> |
            <form action="{{ route('oferta.destroy', $ofertaa->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit">Eliminar</button>
            </form>
        </li>
        @endforeach
    </ul>
</div>
@endsection
