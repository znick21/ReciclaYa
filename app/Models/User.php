<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Definir las columnas que se pueden asignar masivamente
    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    // Las contraseñas deben estar ocultas para evitar la exposición
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Las columnas que deben ser casteadas (convertidas automáticamente)
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relación con las ofertas
    public function ofertas()
    {
        return $this->hasMany(Oferta::class, 'usuario_id');
    }

    // Relación con el historial de aceptación/rechazo de ofertas
    public function historial()
    {
        return $this->hasMany(HistorialOferta::class, 'recolector_id');
    }
}
