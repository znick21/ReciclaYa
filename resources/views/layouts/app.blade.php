<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma de Reciclaje</title>
</head>
<body>
    <nav>
        <ul>
            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('oferta.index') }}">Ofertas</a></li>
            @auth
            <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Cerrar sesión</button>
</form>


            @else
                <li><a href="{{ route('login') }}">Iniciar sesión</a></li>
            @endauth
        </ul>
    </nav>

    <div>
        @yield('content')
    </div>
</body>
</html>
