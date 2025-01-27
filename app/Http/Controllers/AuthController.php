<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:vendedor,recolector',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    // Login de usuario
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros.']);
    }

    // Mostrar formulario de login
    public function loginForm()
    {
        return view('auth.login');
    }

    // Mostrar formulario de edición de perfil
    public function editProfile()
    {
        $user = Auth::user(); // Obtén el usuario autenticado
        return view('profile.edit', compact('user')); // Asegúrate de que la vista es 'profile.edit'
    }


    // Actualizar perfil del usuario
   // Controlador: updateProfile
public function updateProfile(Request $request)
{
    // Validar los datos del formulario
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        'password' => 'nullable|string|min:8|confirmed', // Contraseña opcional
    ]);

    // Obtener el usuario autenticado
    $user = Auth::user();

    // Verificar si el usuario está autenticado y es una instancia de User
    if ($user && $user instanceof User) {
        // Actualizar los datos del usuario
        $user->name = $request->name;
        $user->email = $request->email;

        // Si se proporciona una nueva contraseña, actualizarla
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Guardar los cambios en la base de datos
        $user->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('profile.edit')->with('success', 'Perfil actualizado exitosamente.');
    } else {
        // Si no está autenticado o no es una instancia de User, redirigir al login
        return redirect()->route('login');
    }
}

    
    
    



    // Mostrar formulario de registro
    public function registerForm()
    {
        return view('auth.register');
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
