
@extends('layouts.app')

@section('content')
<form action="{{ route('profile.update') }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Campos para el nombre y el correo -->
    <div class="mb-3">
        <label for="name" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
        @error('name')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
        @error('email')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Campos para la contraseña (solo si se desea cambiarla) -->
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña (opcional)</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Dejar en blanco si no deseas cambiarla">
        <small class="form-text text-muted">La contraseña es opcional. Si deseas cambiarla, ingresa una nueva contraseña.</small>
        @error('password')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repite la nueva contraseña si la cambiaste">
    </div>

    <!-- Botón de Enviar -->
    <button type="submit" class="btn btn-success w-100 mt-3">Actualizar Perfil</button>

    <!-- Mensaje de Éxito -->
    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
</form>

@endsection
<style>
  form {
      max-width: 500px;
      margin: 0 auto;
  }

  .form-group {
      margin-bottom: 15px;
  }

  .form-label {
      font-weight: bold;
      color: #333;
      margin-bottom: 8px;
  }

  .form-control {
      width: 100%;
      padding: 10px;
      border: 2px solid #ddd;
      border-radius: 6px;
      font-size: 14px;
      background-color: #f9f9f9;
  }

  .form-control:focus {
      border-color: #2f8d7c;
      box-shadow: 0 0 8px rgba(47, 141, 124, 0.5);
  }

  .text-danger {
      color: #ff0000;
      font-size: 14px;
      margin-top: 5px;
  }

  .alert-success {
      font-size: 14px;
  }

  .btn-success {
      background-color: #2f8d7c;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      padding: 10px 20px;
      cursor: pointer;
      transition: background-color 0.3s ease-in-out;
  }

  .btn-success:hover {
      background-color: #1e6f56;
  }

  .form-text {
      font-size: 14px;
      color: #777;
  }
</style>
