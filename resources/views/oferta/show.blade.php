@extends('layouts.app')

@section('content')
    <h1>Detalles de la Oferta</h1>
    <p><strong>Vendedor:</strong> {{ $oferta->usuario->name }}</p>
    <p><strong>Latitud:</strong> {{ $oferta->latitud }}</p>
    <p><strong>Longitud:</strong> {{ $oferta->longitud }}</p>
    <p><strong>Cantidad:</strong> {{ $oferta->cantidad }} kg</p>
    <p><strong>Precio:</strong> {{ $oferta->precio }} Bs</p>
    <a href="{{ route('oferta.index') }}">Volver</a>
@endsection
