<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienvenido</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #d4edda; /* Fondo verde claro */
            color: #333;
            font-family: 'Nunito', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .logo img {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #28a745;
        }

        .subtitle {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #555;
        }

        .buttons a {
            text-decoration: none;
            color: #fff;
            background-color: #28a745; /* Botones verdes */
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            margin: 5px;
            display: inline-block;
        }

        .buttons a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo -->
        <div class="logo">
            <img src="{{ asset('images/logory.png') }}" alt="Logo">
        </div>

        <!-- Título -->
        <div class="title">🌱 Bienvenido a ReciclaYa: Transformando el futuro, un reciclaje a la vez ♻️</div>
        <div class="subtitle">En esta app, cada acción cuenta. Aprende, recicla y gana dinero mientras cuidas del planeta. Juntos, convertimos tus desechos en oportunidades para un mundo más verde. 💚
                                    ¡Empieza ahora y sé parte del cambio! 🌍✨</div>

        <!-- Botones -->
        <div class="buttons">
            <a href="{{ route('login') }}">Iniciar Sesión</a>
            <a href="{{ route('register') }}">Registrarse</a>
        </div>
    </div>
</body>
</html>
